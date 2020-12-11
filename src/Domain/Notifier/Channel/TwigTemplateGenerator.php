<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Domain\Notifier\Channel;

use Chemaclass\FinanceYahoo\Domain\Notifier\NotifyResult;
use Twig;

final class TwigTemplateGenerator implements TemplateGeneratorInterface
{
    private Twig\Environment $twig;

    private string $templateName;

    public function __construct(Twig\Environment $twig, string $templateName)
    {
        $this->twig = $twig;
        $this->templateName = $templateName;
    }

    public function generateHtml(NotifyResult $notifyResult): string
    {
        return $this->twig->render(
            $this->templateName,
            [
                'notifyResult' => $notifyResult,
            ]
        );
    }
}
