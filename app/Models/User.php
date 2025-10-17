<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;
use App\Models\Manager\SecretariatSectorManager;


class User extends Authenticatable
{
    // 2. USAR A TRAIT
    use HasFactory, Notifiable, HasUuids, Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        // Adicionar os novos campos aqui!
        'cpf',
        'role_id',
        'secretariat_id',
        'status',
        'phone',
        'cnh',
        'cnh_expiration_date',
        'cnh_category_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function digitalSignature(): HasOne
    {
        return $this->hasOne(DigitalSignature::class);
    }

    /**
     * Get the role of the user
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the secretariat of the user
     */
    public function secretariat(): BelongsTo
    {
        return $this->belongsTo(Secretariat::class);
    }
    /**
     * Get the CNH category of the user
     */
    public function cnhCategory(): BelongsTo
    {
        return $this->belongsTo(CnhCategory::class, 'cnh_category_id');
    }
    /**
     * Check if user has a specific role
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role && $this->role->name === $roleName;
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roleNames): bool
    {
        return $this->role && in_array($this->role->name, $roleNames);
    }

    /**
     * Check if user is a general manager
     */
    public function isGeneralManager(): bool
    {
        return $this->hasRole('general_manager');
    }

    /**
     * Check if user is a sector manager
     */
    public function isSectorManager(): bool
    {
        return $this->hasRole('sector_manager');
    }

    /**
     * Check if user is a driver
     */
    public function isDriver(): bool
    {
        return $this->hasRole('driver');
    }

    /**
     * Check if user is a mechanic
     */
    public function isMechanic(): bool
    {
        return $this->hasRole('mechanic');
    }

    /**
     * Check if user is a manager (general or sector)
     */
    public function isManager(): bool
    {
        return $this->hasAnyRole(['general_manager', 'sector_manager']);
    }

    /**
     * Check if user has higher or equal hierarchy than another user
     */
    public function hasHigherOrEqualHierarchyThan(User $otherUser): bool
    {
        if (!$this->role || !$otherUser->role) {
            return false;
        }

        return $this->role->hierarchy_level >= $otherUser->role->hierarchy_level;
    }

    /**
     * Check if user has higher hierarchy than another user
     */
    public function hasHigherHierarchyThan(User $otherUser): bool
    {
        if (!$this->role || !$otherUser->role) {
            return false;
        }

        return $this->role->hierarchy_level > $otherUser->role->hierarchy_level;
    }

    /**
     * Check if user can manage another user (based on hierarchy)
     */
    public function canManage(User $otherUser): bool
    {
        // General managers can manage everyone
        if ($this->isGeneralManager()) {
            return true;
        }

        // Sector managers can manage drivers and mechanics in their secretariat
        if ($this->isSectorManager()) {
            return $this->secretariat_id === $otherUser->secretariat_id
                && $otherUser->hasAnyRole(['driver', 'mechanic']);
        }

        return false;
    }

    /**
     * Check if user can access a specific vehicle based on logbook permissions
     */
    public function canAccessVehicle(Vehicle $vehicle): bool
    {
        return LogbookPermission::canAccessVehicle($this, $vehicle);
    }

    /**
     * Get all vehicles the user has access to based on logbook permissions
     */
    public function getAccessibleVehicles(): \Illuminate\Database\Eloquent\Collection
    {
        $vehicleIds = LogbookPermission::getUserAccessibleVehicleIds($this);

        if (empty($vehicleIds)) {
            return Vehicle::whereRaw('1 = 0')->get(); // Retorna coleção vazia do Eloquent
        }

        return Vehicle::whereIn('id', $vehicleIds)
            ->get();
    }

    /**
     * Get all secretariats the user has access to based on logbook permissions
     */
    public function getAccessibleSecretariats(): \Illuminate\Database\Eloquent\Collection
    {
        $secretariatIds = LogbookPermission::getUserAccessibleSecretariatIds($this);

        if (empty($secretariatIds)) {
            return Secretariat::whereRaw('1 = 0')->get(); // Retorna coleção vazia do Eloquent
        }

        return Secretariat::whereIn('id', $secretariatIds)->get();
    }

    /**
     * Check if user has any active logbook permissions
     */
    public function chatRooms(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(ChatRoom::class, 'chat_participants', 'user_id', 'chat_room_id')
            ->withPivot('last_read_at')
            ->withTimestamps()
            ->orderBy('updated_at', 'desc');
    }

    /**
     * Get chat messages sent by the user
     */
    public function chatMessages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    /**
     * Check if user is online (can be extended with Redis later)
     */
    public function isOnline(): bool
    {
        // Por enquanto, retorna false. Pode ser implementado com cache/redis
        return cache()->has('user-online-' . $this->id);
    }

    /**
     * Get user's last seen time
     */
    public function getLastSeenAttribute(): ?string
    {
        $lastSeen = cache()->get('user-last-seen-' . $this->id);

        if (!$lastSeen) {
            return null;
        }

        return \Carbon\Carbon::parse($lastSeen)->diffForHumans();
    }

    /**
     * Relação com a foto principal do usuário
     */
    public function photo(): BelongsTo
    {
        return $this->belongsTo(UserPhoto::class, 'photo_id');
    }

    /**
     * Relação com a foto da CNH
     */
    public function photoCnh(): BelongsTo
    {
        return $this->belongsTo(UserPhotoCnh::class, 'photo_cnh_id');
    }

    /**
     * Acessor para a URL da foto do perfil
     */
    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? asset('storage/' . $this->photo->path) : null;
    }

    /**
     * Acessor para a URL da foto da CNH
     */
    public function getPhotoCnhUrlAttribute(): ?string
    {
        return $this->photoCnh ? asset('storage/' . $this->photoCnh->path) : null;
    }

    /**
     * Acessor para o nome da secretaria
     */
    public function getSecretariatNameAttribute(): string
    {
        return $this->secretariat->name ?? 'N/A';
    }

    /**
     * Acessor para verificar se tem CNH cadastrada
     */
    public function getHasCnhAttribute(): bool
    {
        return !empty($this->cnh) && !empty($this->cnh_expiration_date) && !empty($this->cnh_category_id);
    }

    public function getCnhCategoryNameAttribute(): string
    {
        return $this->cnhCategory ? $this->cnhCategory->code : 'N/A';
    }

    /**
     * Acessor para verificar se a CNH está expirada
     */
    public function getIsCnhExpiredAttribute(): bool
    {
        if (!$this->cnh_expiration_date) {
            return false;
        }

        return now()->gt($this->cnh_expiration_date);
    }

    /**
     * Acessor para dias até a expiração da CNH
     */
    public function getDaysUntilCnhExpirationAttribute(): ?int
    {
        if (!$this->cnh_expiration_date) {
            return null;
        }

        return now()->diffInDays($this->cnh_expiration_date, false);
    }

    public function sectorManagerDetails(): HasOne
    {
        return $this->hasOne(SecretariatSectorManager::class);
    }


}
