<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Article_sanitizer
{
    private $allowed_tags = ['p', 'br', 'strong', 'b', 'em', 'i', 'u', 'h2', 'h3', 'ul', 'ol', 'li', 'blockquote', 'a'];

    public function clean($html)
    {
        $html = trim((string) $html);
        if ($html === '') return '';

        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(TRUE);
        $document->loadHTML('<?xml encoding="UTF-8"><div id="article-root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $document->getElementById('article-root');
        if (!$root) return '';

        $this->sanitize_children($root);
        $output = '';
        foreach ($root->childNodes as $child) {
            $output .= $document->saveHTML($child);
        }
        return trim($output);
    }

    private function sanitize_children(DOMNode $parent)
    {
        $blocked_tags = ['script', 'style', 'iframe', 'object', 'embed', 'form', 'input', 'button', 'svg', 'math'];
        for ($index = $parent->childNodes->length - 1; $index >= 0; $index--) {
            $node = $parent->childNodes->item($index);
            if ($node->nodeType !== XML_ELEMENT_NODE) continue;

            $tag = strtolower($node->nodeName);
            if (in_array($tag, $blocked_tags, TRUE)) {
                $parent->removeChild($node);
                continue;
            }
            if (!in_array($tag, $this->allowed_tags, TRUE)) {
                $this->sanitize_children($node);
                while ($node->firstChild) $parent->insertBefore($node->firstChild, $node);
                $parent->removeChild($node);
                continue;
            }

            for ($attribute = $node->attributes->length - 1; $attribute >= 0; $attribute--) {
                $name = strtolower($node->attributes->item($attribute)->name);
                if ($tag !== 'a' || !in_array($name, ['href', 'target', 'rel'], TRUE)) {
                    $node->removeAttribute($name);
                }
            }

            if ($tag === 'a') {
                $href = trim($node->getAttribute('href'));
                if ($href !== '' && !preg_match('#^(https?://|mailto:|/)#i', $href)) $node->removeAttribute('href');
                if (!$node->hasAttribute('href')) {
                    $node->removeAttribute('target');
                    $node->removeAttribute('rel');
                } elseif ($node->getAttribute('target') === '_blank') {
                    $node->setAttribute('rel', 'noopener noreferrer');
                } else {
                    $node->removeAttribute('target');
                    $node->removeAttribute('rel');
                }
            }
            $this->sanitize_children($node);
        }
    }
}
