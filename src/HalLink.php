<?php

declare(strict_types=1);

namespace BinSoul\Net\Hal\Client;

use Rize\UriTemplate\UriTemplate;

/**
 * Represents a link.
 */
class HalLink
{
    /**
     * @var UriTemplate|null
     */
    private static $uriTemplate;

    /**
     * @var string
     */
    private $href;

    /**
     * @var bool|null
     */
    private $templated;

    /**
     * @var string|null
     */
    private $type;

    /**
     * @var string|null
     */
    private $deprecation;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var string|null
     */
    private $profile;

    /**
     * @var string|null
     */
    private $title;

    /**
     * @var string|null
     */
    private $hreflang;

    /**
     * Constructs an instance of this class.
     */
    public function __construct(
        string $href,
        ?bool $isTemplated = null,
        ?string $type = null,
        ?string $deprecation = null,
        ?string $name = null,
        ?string $profile = null,
        ?string $title = null,
        ?string $hreflang = null
    ) {
        $this->href = $href;
        $this->templated = $isTemplated;
        $this->type = $type;
        $this->deprecation = $deprecation;
        $this->name = $name;
        $this->profile = $profile;
        $this->title = $title;
        $this->hreflang = $hreflang;
    }

    /**
     * Returns the expanded URI.
     *
     * @param mixed[] $variables
     */
    public function getUri(array $variables = []): string
    {
        $uri = $this->href;

        if ($this->templated) {
            $uri = self::expandUriTemplate($uri, $variables);
        }

        return $uri;
    }

    /**
     * Returns either a URI [RFC3986] or a URI template [RFC6570].
     */
    public function getHref(): string
    {
        return $this->href;
    }

    /**
     * Sets the href property.
     */
    public function setHref(string $href): void
    {
        $this->href = $href;
    }

    /**
     * Indicates if the "href" property is a URI template.
     */
    public function isTemplated(): bool
    {
        return (bool) $this->templated;
    }

    /**
     * Sets the templated property.
     */
    public function setTemplated(?bool $templated): void
    {
        $this->templated = $templated;
    }

    /**
     * Indicates the media type expected when dereferencing the target resource.
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Sets the type property.
     */
    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    /**
     * Indicates if the link is to be deprecated (i.e. removed) at a future date.
     */
    public function isDeprecated(): bool
    {
        return ((string) $this->deprecation) !== '';
    }

    /**
     * Returns an URI which provides further information about the deprecation.
     */
    public function getDeprecation(): ?string
    {
        return $this->deprecation;
    }

    /**
     * Sets the deprecation property.
     */
    public function setDeprecation(?string $deprecation): void
    {
        $this->deprecation = $deprecation;
    }

    /**
     * Returns a value which may be used as a secondary key for selecting links which share the same relation type.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Sets the name property.
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * Returns a value which is a URI that hints about the profile of the target resource.
     */
    public function getProfile(): ?string
    {
        return $this->profile;
    }

    /**
     * Sets the profile property.
     */
    public function setProfile(?string $profile): void
    {
        $this->profile = $profile;
    }

    /**
     * Returns a value which labels the destination of a link with a human-readable identifier.
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Sets the title property.
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * Indicates what the language of the result of dereferencing the link should be.
     */
    public function getHreflang(): ?string
    {
        return $this->hreflang;
    }

    /**
     * Sets the hreflang property.
     */
    public function setHreflang(?string $hreflang): void
    {
        $this->hreflang = $hreflang;
    }

    /**
     * Serializes this link to an array.
     *
     * @return mixed[]
     */
    public function toArray(): array
    {
        $result = [];

        foreach (get_object_vars($this) as $attr => $val) {
            if ($val !== null) {
                $result[$attr] = $val;
            }
        }

        return $result;
    }

    /**
     * Expands the URI template with the given variables.
     *
     * @param mixed[] $variables
     */
    private static function expandUriTemplate(string $template, array $variables): string
    {
        if (self::$uriTemplate === null) {
            self::$uriTemplate = new UriTemplate();
        }

        return self::$uriTemplate->expand($template, $variables);
    }
}
