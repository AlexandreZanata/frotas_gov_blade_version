<?php
namespace Database\Seeders\checklist;
use App\Models\checklist\Checklist;
use App\Models\checklist\ChecklistAnswer;
use App\Models\checklist\ChecklistItem;
use App\Models\run\Run;
use Illuminate\Database\Seeder;

class ChecklistSeeder extends Seeder
{
    public function run(): void
    {
        $run = Run::first(); // Pega a primeira corrida criada
        $items = ChecklistItem::all();

        if ($run && $items->isNotEmpty()) {
            // Cria a "folha" de checklist
            $checklist = Checklist::create([
                'run_id' => $run->id,
                'user_id' => $run->user_id,
            ]);

            // Cria as respostas para cada item
            foreach ($items as $item) {
                $status = 'ok';
                $notes = null;
                if ($item->name === 'Calibragem dos Pneus') {
                    $status = 'attention';
                    $notes = 'Pneu dianteiro direito parece baixo.';
                }

                ChecklistAnswer::create([
                    'checklist_id' => $checklist->id,
                    'checklist_item_id' => $item->id,
                    'status' => $status,
                    'notes' => $notes,
                ]);
            }
        }
    }
}
