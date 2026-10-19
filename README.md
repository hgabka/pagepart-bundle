# hgabka/pagepart-bundle

Oldalrész- (pagepart) alapú tartalomszerkesztés: egy oldal tartalma előre definiált típusú, tetszőlegesen sorrendezhető blokkokból áll (szöveg, fejléc, kép, link, nyers HTML, …), amelyeket a szerkesztők az admin felületen adhatnak hozzá. Az oldalsablonok (page template) sorokra/régiókra osztják az oldalt, és minden régióhoz külön lehet megadni, milyen oldalrészek vehetők fel. A Kunstmaan PagePartBundle koncepcióján alapul; a [`hgabka/node-bundle`](../node-bundle) oldalaihoz készült, de bármely entitáshoz használható, ami megvalósítja a `HasPagePartsInterface`-t.

Régebbi, angol nyelvű leírás: [src/Resources/doc/PagePartBundle.md](src/Resources/doc/PagePartBundle.md).

## Telepítés

```bash
composer require hgabka/pagepart-bundle
```

```php
// config/bundles.php
Hgabka\PagePartBundle\HgabkaPagePartBundle::class => ['all' => true],
```

```yaml
# config/routes.yaml
hgabka_pagepart:
    resource: '@HgabkaPagePartBundle/Resources/config/routing.yaml'
```

Függőség: `hgabka/utils-bundle`. Táblák: `hg_page_part_page_part_refs`, `hg_page_part_page_template_configuration` + az egyes oldalrész-típusok táblái (migrációval hozd létre).

## Konfiguráció

```yaml
# config/packages/hgabka_page_part.yaml
hgabka_page_part:
    extended_pagepart_chooser: false   # bővített oldalrész-választó az adminban
    pageparts: []                      # opcionális: inline preset-ek (ugyanaz, mint a config/pageparts/*.yaml)
    pagetemplates: []                  # opcionális: inline sablon-preset-ek
```

### Oldalrész-konfigurációk (`config/pageparts/<név>.yaml`)

Egy *context* (pl. `main`, `banners`, `footer`) beállítása: mely típusok vehetők fel.

```yaml
# config/pageparts/main.yaml
name: Main
context: main
config: { viewCss: '/css/admin/cms/content.css', editorCss: '/css/admin/cms/content_edit.css' }
types:
    - { name: Szöveg,  class: Hgabka\PagePartBundle\Entity\TextPagePart }
    - { name: Fejléc,  class: Hgabka\PagePartBundle\Entity\HeaderPagePart }
    - { name: Kép,     class: Hgabka\PagePartBundle\Entity\ImagePagePart }
    - { name: Videó,   class: App\Entity\PageParts\VideoPagePart, pagelimit: 1 }
# további kulcsok: extends (másik konfig kiterjesztése), widget_template, types[].preview, types[].pagelimit
```

A konfigurációt a projekt `config/pageparts/` mappájából, vagy `Bundle:név` hivatkozással bundle `Resources/config/pageparts/` mappájából tölti be a `PagePartConfigurationParser`.

### Oldalsablonok (`config/pagetemplates/<név>.yaml`)

```yaml
# config/pagetemplates/contentpage.yaml
name: "Content page"
rows:
    - regions:
        - { name: "main", span: 12 }
template: "Pages\\ContentPage\\pagetemplate.html.twig"
```

A sorok és régiók (`PageTemplate\Row`, `Region`, `span` a 12 oszlopos rácsban) az adminban a sablon szerint jelennek meg, a `template` Twig fájl pedig a front-end megjelenítést adja.

## Használat

### Az oldal-entitás

```php
class ContentPage extends AbstractPage implements HasPageTemplateInterface
{
    public function getPagePartAdminConfigurations(): array
    {
        return ['main'];            // config/pageparts/main.yaml
    }

    public function getPageTemplates(): array
    {
        return ['contentpage'];     // config/pagetemplates/contentpage.yaml
    }
}
```

- `HasPagePartsInterface` – `getId()`, `getPagePartAdminConfigurations()` (konfig-nevek vagy `PagePartAdminConfiguratorInterface` példányok)
- `HasPageTemplateInterface` – ezen felül `getPageTemplates()`

Az entitás és az oldalrész között a `PagePartRef` (`page_id`, `page_entityname`, `context`, `sequencenumber`, `page_part_id`, `page_part_entityname`) rekord teremt kapcsolatot, a sablon-választást a `PageTemplateConfiguration` tárolja.

### Megjelenítés Twigben

```twig
{{ render_pagetemplate(page) }}           {# a kiválasztott oldalsablon #}
{{ render_pageparts(page, 'main') }}      {# egy context oldalrészei sorrendben #}
{{ getpageparts(page, 'main') }}          {# a nyers oldalrész-entitások #}
```

További függvények: `getpagetemplate`, `render_pagetemplate_configuration`, `pagepartadmin_widget` (admin).

### Saját oldalrész-típus

```php
use Hgabka\PagePartBundle\Entity\AbstractPagePart;

#[ORM\Entity]
#[ORM\Table(name: 'app_video_page_parts')]
class VideoPagePart extends AbstractPagePart
{
    #[ORM\Column(type: 'string')]
    private ?string $url = null;

    public function getDefaultView(): string      { return 'PageParts/VideoPagePart/view.html.twig'; }
    public function getAdminView(): string        { return 'PageParts/VideoPagePart/admin-view.html.twig'; }
    public function getDefaultAdminType(): string { return VideoPagePartAdminType::class; }
}
```

(`PagePartInterface`: `getDefaultView`, `getAdminView`, `getView(?HasPagePartsInterface $page)`, `getDefaultAdminType`.) Az admin űrlap `AbstractPagePartAdminType`-ból származzon.

### Beépített oldalrészek

| Entitás | Tartalom |
|---|---|
| `TextPagePart` | szöveg (WYSIWYG) |
| `HeaderPagePart` | fejléc (szint + szöveg) |
| `ImagePagePart` | kép a `media-bundle`-ből, alt szöveg, link |
| `LinkPagePart` | link (URL, szöveg, új ablak) |
| `LinePagePart` | vízszintes vonal |
| `RawHTMLPagePart` | nyers HTML |
| `TocPagePart` | tartalomjegyzék |
| `ToTopPagePart` | „oldal tetejére” link |

### Kód alapú létrehozás

A `Helper\Services\PagePartCreatorService` (`addPagePartToPage()`, `addPagePartsToPage()`) oldalrészek programozott felvételére szolgál (pl. fixture-ökben, importokban).

## Események és integráció

- `Event\Events::POST_PERSIST` (`hgabka_pagepart.postPersist`, `PagePartEvent`) – oldalrész mentése után.
- `EventListener\NodeListener` – a node-bundle `AdaptFormEvent`-jére kiegészíti az oldal szerkesztő űrlapját az oldalrész- és oldalsablon-widgetekkel; `EventListener\CloneListener` – az utils-bundle `DeepCloneAndSaveEvent`-jére klónozáskor lemásolja az oldalrészeket is.
