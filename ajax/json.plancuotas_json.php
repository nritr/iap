<?php
$fullpath = str_replace("\\","/",$_SERVER['REQUEST_URI']);
$fisical = str_replace(($fullpath == "/" ? "" : $fullpath),"",str_replace("\\","/",getcwd()));
$path = str_replace((dirname($_SERVER['PHP_SELF']) == "/" ? "" : dirname($_SERVER['PHP_SELF'])),"",$fisical);
include_once $path."/admin/config.php";
include_once $path."/admin/php/utils/DB_connect.php";
include_once $path."/admin/php/utils/RequestUtil.php";
include_once $path."/admin/php/utils/GeneralUtils.php";
include_once $path."/admin/php/services/UsuarioService.php";
include_once $path."/admin/php/services/PlanCuotaService.php";
include_once $path."/admin/php/utils/ErrorManager.php";

$errorManager   = new ErrorManager();
$request        = new RequestUtil($_POST['data']);
$planService    = new PlanCuotaService();

$tipo           = $request->getArrayValue("tipo");

if ($tipo=="load_montos") {
    try {
        $monto                  = $request->getArrayValue("monto");
        $filter                 = new PlanCuotaFilter();
        $filter->montoPlanMax   = $monto;
        $filter->groupBy        = " p.monto_plan ";
        $list                   = $planService->load($filter);
        $errorManager->setData($list);
    } catch (Exception $ex) {
        $errorManager = new ErrorManager($ex);
    }
} else if ($tipo=='load_cuotas') {
    try {
        $monto                  = $request->getArrayValue("monto");
        $frecuencia             = $request->getArrayValue("frecuencia");
        $filter                 = new PlanCuotaFilter();
        $filter->montoPlan      = $monto;
        $filter->frecuenciaPlan = $frecuencia;
        $filter->orderBy        = "p.cuotas";
        $list                   = $planService->load($filter);
        $errorManager->setData($list);
    } catch (Exception $ex) {
        $errorManager = new ErrorManager($ex);
    }
}
echo $errorManager->getJsonResponse();
?>