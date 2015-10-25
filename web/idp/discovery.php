<?php

require_once __DIR__.'/_config.php';

$all = IdpConfig::current()->getBuildContainer()->getPartyContainer()->getSpEntityDescriptorStore()->all();
switch (count($all)) {
    case 0:
        print "None SP configured";
        exit;
    case 1:
        header('Location: login.php?idp='.$all[0]->getEntityID());
        exit;
}

print "<h1>Following SPs are configured</h1>\n";
print "<p><small>Choose one for IDP initiated SSO</small></p>\n";
foreach ($all as $idp) {
    if ($idp->getAllSpSsoDescriptors()) {
        print "<p><a href=\"initiate.php?sp={$idp->getEntityID()}\">{$idp->getEntityID()}</a></p>\n";
    }
}
print "\n<p>LigthSAML-IDP</p>\n";
