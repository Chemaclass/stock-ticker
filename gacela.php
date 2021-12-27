<?php

declare(strict_types = 1);

use Gacela\Framework\AbstractConfigGacela;

return static function () {
    return new class() extends AbstractConfigGacela {
        public function config(): array
        {
            return [
                'type' => 'env',
                'path' => '.env.dist',
                'path_local' => '.env',
            ];
        }
    };
};
