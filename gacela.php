<?php

declare(strict_types=1);

use Gacela\Framework\Bootstrap\GacelaConfig;
use Gacela\Framework\Config\ConfigReader\EnvConfigReader;

return static function (GacelaConfig $config): void {
    $config->addAppConfig('config/.env*', 'config/.env.local.dist', EnvConfigReader::class);
};
