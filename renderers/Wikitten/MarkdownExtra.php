<?php
namespace Wikitten;

class MarkdownExtra extends \Michelf\MarkdownExtra
{
    /**
     * Get the URI path, without its last file
     *
     * @param string $address
     *
     * @return string
     */
    protected static function getUriPath($address)
    {
        $path = (false === empty(pathinfo($address)['extension']))? dirname($address): $address;
        $path = ('.' === $path || '..' === $path)? '': $path;
        return ('/' === $path[strlen($path)-1])? $path: $path . '/';
    }

    /**
     * Get the current URI path, without its last file
     *
     * @return string
     */
    protected static function getCurrentUriPath()
    {
        return self::getUriPath($_SERVER['REQUEST_URI']);
    }

    /**
     * Check if a URL is external
     *
     * @param string $address
     *
     * @return boolean
     */
    protected static function isExternal($address)
    {
        try {
            if (false === empty(preg_match('/^(?!www\.|(?:http|ftp)s?:\/\/|[A-Za-z]:\\|\/\/).*/', trim($address)))) {
                return false;
            }
            $domain = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
            $parsed = parse_url($address);
            if (false === empty($parsed['scheme']) || false === empty($parsed['host']) || false === empty($parsed['port'])) {
                $parsedDomain = ((false === empty($parsed['scheme']))? $parsed['scheme']: '') . '://';
                $parsedDomain .= (false === empty($parsed['host']))? $parsed['host']: '';
                $parsedDomain .= (false === empty($parsed['port']))? ':' . $parsed['port']: '';
                return !($domain === $parsedDomain);
            }
            return true;
        } catch (\Exception $ex) {
            return true;
        }
    }

    /**
     * Add the target property to a link tag
     *
     * @param string $element
     * @param string $url
     *
     * @return string
     */
    protected function addTargetToAnchors($element, $url)
    {
        if (false === $this->isExternal($url)) {
            return $element;
        }
        if (false !== strpos($element, 'target="')) {
            $element = preg_replace('/target="[^\"]+"/', 'target="' . EXTERNAL_LINK_TARGET . '"', $element);
        } else {
            $element = str_replace('a href=', 'a target="' . EXTERNAL_LINK_TARGET . '" href=', $element);
        }

        return $element;
    }

    /**
     * Callback for reference anchors
     * @param  array $matches
     * @return string
     */
    protected function _doAnchors_reference_callback($matches) {
        $whole_match =  $matches[1];
        $link_text   =  $matches[2];
        $link_id     =& $matches[3];

        if ($link_id == "") {
            // for shortcut links like [this][] or [this].
            $link_id = $link_text;
        }

        // lower-case and turn embedded newlines into spaces
        $link_id = strtolower($link_id);
        $link_id = preg_replace('{[ ]?\n}', ' ', $link_id);

        if (isset($this->urls[$link_id])) {
            $url = $this->urls[$link_id];
            $url = $this->encodeURLAttribute($url);

            $result = "<a href=\"$url\"";
            if ( isset( $this->titles[$link_id] ) ) {
                $title = $this->titles[$link_id];
                $title = $this->encodeAttribute($title);
                $result .=  " title=\"$title\"";
            }
            if (isset($this->ref_attr[$link_id]))
                $result .= $this->ref_attr[$link_id];

            $link_text = $this->runSpanGamut($link_text);
            $result .= ">$link_text</a>";
            $result = $this->addTargetToAnchors($result, $url);
            $result = $this->hashPart($result);
        }
        else {
            $result = $whole_match;
        }
        return $result;
    }

    /**
     * Callback for inline anchors
     * @param  array $matches
     * @return string
     */
    protected function _doAnchors_inline_callback($matches) {
        $whole_match    =  $matches[1];
        $link_text        =  $this->runSpanGamut($matches[2]);
        $url            =  $matches[3] == '' ? $matches[4] : $matches[3];
        $title            =& $matches[7];
        $attr  = $this->doExtraAttributes("a", $dummy =& $matches[8]);

        // if the URL was of the form <s p a c e s> it got caught by the HTML
        // tag parser and hashed. Need to reverse the process before using the URL.
        $unhashed = $this->unhash($url);
        if ($unhashed != $url)
            $url = preg_replace('/^<(.*)>$/', '\1', $unhashed);

        $url = $this->encodeURLAttribute($url);

        $result = "<a href=\"$url\"";
        if (isset($title)) {
            $title = $this->encodeAttribute($title);
            $result .=  " title=\"$title\"";
        }
        $result .= $attr;

        $link_text = $this->runSpanGamut($link_text);
        $result .= ">$link_text</a>";
        $result = $this->addTargetToAnchors($result, $url);

        return $this->hashPart($result);
    }
}
