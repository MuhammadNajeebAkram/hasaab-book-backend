<?php

use App\Http\Controllers\AdvanceSalaryController;
use App\Http\Controllers\AdvanceSalaryEntryController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BonusController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\COAController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\EmployeeBonusController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeDesignationController;
use App\Http\Controllers\EmployeeLoanController;
use App\Http\Controllers\EmployeeLoanEntryController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\VoucherController;
use App\Models\AdvanceSalaryEntry;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::get('user', [AuthController::class, 'getAuthenticatedUser'])->middleware('auth:api');

Route::middleware(['auth:api'])->group(function () {

    Route::get('/get_coa_by_level/{level}', [COAController::class, 'getCOAByLevel']);
    Route::get('/get_coa_children/{parent}', [COAController::class, 'getCOAChildren']);
    Route::post('/save_coa', [COAController::class, 'saveCOA']);
    Route::post('/update_coa', [COAController::class, 'updateCOA']);
    Route::get('/activate_coa/{id}/{status}', [COAController::class, 'activateAccount']);
    Route::get('/get_all_coa_by_parent/{parent}', [COAController::class, 'getAllCOAByParent']);
    Route::get('/get_all_coa_cash_or_bank/{is_cash}/{is_bank}', [COAController::class, 'getCashOrBankAccount']);
    Route::get('/get_salaries_accounts/{is_salary}/{is_advance}/{is_loan}', [COAController::class, 'getSalariesAccounts']);
    Route::get('get_child_of_system_coa/{id}', [COAController::class, 'getChildOfSystemCOA']);
   

    Route::post('save_draft_voucher', [VoucherController::class, 'saveDraftVoucher']);
    Route::post('update_draft_voucher', [VoucherController::class, 'updateDraftVoucher']);
    Route::get('get_postable_vouchers/{type}', [VoucherController::class, 'getPostableVouchers']);
    Route::get('get_voucher_entries/{id}', [VoucherController::class, 'getVoucherEntries']);

    Route::post('save_province', [ProvinceController::class, 'saveProvince']);
    Route::post('update_province', [ProvinceController::class, 'updateProvince']);
    Route::get('get_provinces', [ProvinceController::class, 'getProvinces']);

    Route::post('save_division', [DivisionController::class, 'saveDivision']);
    Route::post('update_division', [DivisionController::class, 'updateDivision']);
    Route::get('get_divisions/{province}', [DivisionController::class, 'getDivisions']);

    Route::post('save_district', [DistrictController::class, 'saveDistrict']);
    Route::post('update_district', [DistrictController::class, 'updateDistrict']);
    Route::get('get_districts/{division}', [DistrictController::class, 'getDistricts']);

    Route::post('save_city', [CityController::class, 'saveCity']);
    Route::post('update_city', [CityController::class, 'updateCity']);
    Route::get('get_cities/{district}', [CityController::class, 'getCities']);

    Route::post('save_branch', [BranchController::class, 'saveBranch']);
    Route::post('update_branch', [BranchController::class, 'updateBranch']);
    Route::get('get_branches', [BranchController::class, 'getBranches']);

    Route::post('save_designation', [EmployeeDesignationController::class, 'saveDesignation']);
    Route::post('update_designation', [EmployeeDesignationController::class, 'updateDesignation']);
    Route::get('get_designations', [EmployeeDesignationController::class, 'getDesignations']);

    Route::post('save_employee', [EmployeeController::class, 'saveEmployee']);
    Route::post('update_employee', [EmployeeController::class, 'updateEmployee']);
    Route::get('get_employees', [EmployeeController::class, 'getEmployees']);
    Route::get('get_employees_with_accounts', [EmployeeController::class, 'getEmployeesWithSalaryAccounts']);
    Route::get('get_employee_salary_info/{id}', [EmployeeController::class, 'getEmployeeSalaryInfo']);

    Route::post('save_advance_salary', [AdvanceSalaryController::class, 'saveAdvances']);
    Route::post('update_advance_salary', [AdvanceSalaryController::class, 'updateAdvances']);
    Route::get('get_advance_entry_by_voucher/{id}', [AdvanceSalaryController::class, 'getAdvanceSalaryByVoucher']);

    Route::post('save_employee_loan', [EmployeeLoanController::class, 'saveLoan']);
    Route::post('update_employee_loan', [EmployeeLoanController::class, 'updateLoan']);
    Route::get('get_employee_loan_by_voucher/{id}', [EmployeeLoanController::class, 'getLoanByVoucher']);

    Route::get('get_employee_advance_balance/{id}', [AdvanceSalaryEntryController::class, 'getBalance']);

    Route::get('get_employee_loan_installment/{id}', [EmployeeLoanEntryController::class, 'getInstallment']);

    Route::post('save_salary', [SalaryController::class, 'saveSalary']);
    Route::post('update_salary', [SalaryController::class, 'updateSalary']);
    Route::get('get_employee_salary_by_voucher/{id}', [SalaryController::class, 'getSalaryByVoucher']);

    Route::get('get_bonuses', [BonusController::class, 'getBonuses']);
    Route::post('save_employee_bonus', [EmployeeBonusController::class, 'saveEmployeeBonus']);
    Route::post('update_employee_bonus', [EmployeeBonusController::class, 'updateEmployeeBonus']);
    Route::get('get_bonus_entry_by_voucher/{id}', [EmployeeBonusController::class, 'getBonusByVoucher']);



 });
 //Route::get('/get_coa_by_level/{level}', [COAController::class, 'getCOAByLevel']);
