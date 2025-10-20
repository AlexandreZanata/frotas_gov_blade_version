<?php

namespace App\Http\Controllers\fuel;

use App\Http\Controllers\Controller;
use App\Models\fuel\FuelCalculationMethod;
use App\Models\fuel\FuelDiscountSetting;
use App\Models\fuel\FuelType;
use Illuminate\Http\Request;

class FuelQuotationSettingsController extends Controller
{
    public function index()
    {
        $fuelTypes = FuelType::with(['calculationMethods', 'discountSettings'])->get();

        return view('fuel-quotations.settings', compact('fuelTypes'));
    }

    // Nos métodos store e update, redirecionar mantendo a aba
    public function storeCalculationMethod(Request $request)
    {
        $validated = $request->validate([
            'fuel_type_id' => 'required|exists:fuel_types,id',
            'name' => 'required|string|max:255',
            'calculation_type' => 'required|in:average,weighted_average,custom',
            'formula' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['order'] = $validated['order'] ?? 0;

        FuelCalculationMethod::create($validated);

        $activeTab = $request->get('active_tab', 'calculation');

        return redirect()->route('fuel-quotations.settings', ['tab' => $activeTab])
            ->with('success', 'Método de cálculo criado com sucesso!');
    }

    public function updateCalculationMethod(Request $request, FuelCalculationMethod $method)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'calculation_type' => 'required|in:average,weighted_average,custom',
            'formula' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $method->update($validated);

        $activeTab = $request->get('active_tab', 'calculation');

        return redirect()->route('fuel-quotations.settings', ['tab' => $activeTab])
            ->with('success', 'Método de cálculo atualizado com sucesso!');
    }

    public function destroyCalculationMethod(FuelCalculationMethod $method)
    {
        $method->delete();

        return redirect()->route('fuel-quotations.settings', ['tab' => 'calculation'])
            ->with('success', 'Método de cálculo removido com sucesso!');
    }

    public function storeDiscountSetting(Request $request)
    {
        $validated = $request->validate([
            'fuel_type_id' => 'required|exists:fuel_types,id',
            'name' => 'required|string|max:255',
            'discount_type' => 'required|in:percentage,fixed,custom',
            'percentage' => 'nullable|numeric|min:0|max:100',
            'fixed_value' => 'nullable|numeric|min:0',
            'order' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['order'] = $validated['order'] ?? 0;
        $validated['percentage'] = $validated['percentage'] ?? 0;
        $validated['fixed_value'] = $validated['fixed_value'] ?? 0;

        FuelDiscountSetting::create($validated);

        $activeTab = $request->get('active_tab', 'discount');

        return redirect()->route('fuel-quotations.settings', ['tab' => $activeTab])
            ->with('success', 'Desconto criado com sucesso!');
    }

    public function updateDiscountSetting(Request $request, FuelDiscountSetting $discount)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'discount_type' => 'required|in:percentage,fixed,custom',
            'percentage' => 'nullable|numeric|min:0|max:100',
            'fixed_value' => 'nullable|numeric|min:0',
            'order' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $discount->update($validated);

        $activeTab = $request->get('active_tab', 'discount');

        return redirect()->route('fuel-quotations.settings', ['tab' => $activeTab])
            ->with('success', 'Desconto atualizado com sucesso!');
    }

    public function destroyDiscountSetting(FuelDiscountSetting $discount)
    {
        $discount->delete();

        return redirect()->route('fuel-quotations.settings', ['tab' => 'discount'])
            ->with('success', 'Desconto removido com sucesso!');
    }
}
