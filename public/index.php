<?php

checkAuth();

$randomName = random_int(10000001, 999999999999) . time() . '-generatedSatis';

createAndUpdateSatis($_GET['repos'], $randomName);

// Build the JSON file under a random name using the newly generated satis.json
system(PHP_BINDIR . "/php7.4 ../bin/satis build ../$randomName.json ../generated/ >/dev/null && cat ../generated/include/* > ../built-$randomName.json");

echo file_get_contents("../built-$randomName.json");

// Delete generate satis and packages json files
unlink("../$randomName.json");
unlink("../built-$randomName.json");

/**
 * Match the received password.
 * This is hardcoded password Satis expects to receive from marketplace.
 */
function checkAuth(): void
{
    if ($_GET['password'] !== $_ENV['PASSWORD']) {
        exit;
    }
}

/**
 * Copies the initial template of satis.json file under a random (timestamped) name.
 * Add the repositories in newly generated satis.json file.
 */
function createAndUpdateSatis(array $repos, string $randomName)
{
    copy('../satis.json', "../$randomName.json");

    $satis = json_decode(file_get_contents("../$randomName.json"), true);

    foreach ($repos as $repo) {
        $satis['repositories'][] = [ 'type' => 'vcs', 'url' => "https://github.com/$repo" ];
    }

    file_put_contents("../$randomName.json", json_encode($satis));
}
