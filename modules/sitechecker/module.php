<?php
function siteCheckerModRoutes() {
    $siteCheckerModRoutes = array(
        "init-probemanager" => array(
            "callback" => "runProbes",
            "files" => array(
                "FileSystem.php",
                "DomainRecord.php",
                "DomainManager.php",
                "Probe.php",
                "ProbeException.php",
                "DomainManagerException.php",
                "ProbeRenderer.php",
                "ProbeResult.php"
            )
        )
    );
    return $siteCheckerModRoutes;
}

function runProbes() {
    $probeManager = DomainManager::newFromFileSystem('C:\\wamp64\\www\\trust\\appserver\\site-json\\');
    $probeManager->doValidation();
    $probeManager->doProbes();
    $probeManager->renderOutput();
}