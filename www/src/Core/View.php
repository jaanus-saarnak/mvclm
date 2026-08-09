<?php
/**
 * Template rendering.
 *
 * The view file is captured into an output buffer, then handed to a layout in
 * App/Views/layouts/ as $content. If the layout file is unreadable the view is
 * echoed bare, which keeps templates written before layouts existed working.
 *
 * extract() expands the $content array into scope before the file is required,
 * so a view reads plain $variables rather than an array. Flash messages are
 * pushed in on every render as $flash_messages, whether or not the view uses
 * them.
 *
 * A view chooses its layout by setting $layout at the top of the file, like
 * $layout = 'dashboard'. A view that sets nothing gets main.
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

declare(strict_types=1);

namespace Core;

use RuntimeException;
use App\Flash;

/**
 * Handles template rendering with layout support.
 */
class View
{
    /**
     * Render a template file with optional layout.
     *
     * @param string $template The relative path to the template file
     * @param array $content Data to be displayed in the template
     * @return void
     * @throws RuntimeException If template or layout file cannot be read
     */
    public static function renderTemplate(string $template, array $content = []): void
    {
        $content['flash_messages'] = Flash::getMessages();
        
        $layout = $content['layout'] ?? 'main';
        
        ob_start();
        $file = CONFIG['path'] . $template;
        
        if (!is_readable($file)) {
            throw new RuntimeException("Template file {$file} not found or not readable.");
        }
        
        extract($content);
        require $file;
        $viewContent = ob_get_clean();
        
        $layoutFile = CONFIG['path'] . '/App/Views/layouts/' . $layout . '.php';
        
        if (!is_readable($layoutFile)) {
            // Fallback for backward compatibility
            echo $viewContent;
            return;
        }
        
        $content['content'] = $viewContent;
        extract($content);
        require $layoutFile;
    }

    /**
     * Render pagination links.
     *
     * @param array $pageLinks Array of page link data
     * @return string|false HTML string or false if no pagination needed
     */
    public static function renderPageLinks(array $pageLinks): string|false
    {
        if (count($pageLinks) <= 1) {
            return false;
        }

        $html = '<nav class="page-links" aria-label="Page links"><ul class="pagination justify-content-center">' . PHP_EOL;
        
        foreach ($pageLinks as $pageLink) {
            $addClass = '';
            if (!empty($pageLink['active'])) {
                $addClass .= ' active';
            }
            if (!empty($pageLink['disabled'])) {
                $addClass .= ' disabled';
            }

            $href = (string)$pageLink['href'];
            $txt = (string)$pageLink['txt'];

            $html .= '<li class="page-item' . $addClass . '">' . PHP_EOL;
            $html .= '<a class="page-link" href="' . $href . '">' . $txt . '</a>' . PHP_EOL;
            $html .= '</li>' . PHP_EOL;
        }

        $html .= '</ul></nav>' . PHP_EOL;
        return $html;
    }
}

