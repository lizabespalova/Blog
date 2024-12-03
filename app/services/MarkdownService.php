<?php

namespace services;

use Parsedown;

class MarkdownService
{
    // Функция для парсинга Markdown
    public function parseMarkdown($markdownContent): string
    {
        return (new Parsedown())->text($markdownContent);
    }
}