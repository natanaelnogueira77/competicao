<?php

namespace Src\App\Controllers\Web;

use GTG\MVC\Controller;
use Src\Components\Theme;
use Src\Data\MenuData;
use Src\Models\Config;

class TemplateController extends Controller 
{
    public function addData(): void 
    {
        $configMetas = (new Config())->getGroupedMetas([
            Config::KEY_LOGO, 
            Config::KEY_LOGO_ICON, 
            Config::KEY_STYLE
        ]);

        $user = $this->session->getAuth() ? $this->session->getAuth() : null;

        $this->addViewData([
            'theme' => (new Theme())->loadData([
                'logo' => $configMetas && $configMetas[Config::KEY_LOGO] ? url($configMetas[Config::KEY_LOGO]) : '',
                'logo_icon' => $configMetas && $configMetas[Config::KEY_LOGO_ICON] ? url($configMetas[Config::KEY_LOGO_ICON]) : '',
                'loading_text' => _('Aguarde, carregando...'),
                'has_header' => true,
                'has_footer' => true,
                'header' => [
                    'menu' => MenuData::getHeaderMenuItems($this->router, $user),
                    'right' => [
                        'items' => MenuData::getRightMenuItems($this->router, $user),
                        'languages' => [
                            'heading' => _('Linguagens'),
                            'curr_img' => url("public/imgs/flags/{$this->session->getLanguage()[1]}.png"),
                            'items' => [
                                ['url' => $this->getRoute('language.index', ['lang' => 'pt']), 'desc' => _('Português')]
                            ]
                        ]
                    ]
                ]
            ])
        ]);
    }
}