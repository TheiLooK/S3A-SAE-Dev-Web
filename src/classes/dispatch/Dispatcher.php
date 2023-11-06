<?php
declare(strict_types=1);
namespace touiteur\app\dispatch;

use touiteur\app\action\Action;
class Dispatcher {


    private ?string $action;

    public function run(): void {
        switch ($this->action) {
            case '1':
                $result = "a définir ";
                break;
            case '2':
                $result = " a définir ";
                break;
            default :
                $result = "a définir ";
        }
        $this->renderPage($result);
    }

    private function renderPage(string $html): void {
        echo <<<END
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <title>Touiteur</title>
    </head>
    <body>
    <header>
        <nav>
            <div> 
                <h1>Deefy</h1>
            </div>
            <ul>
            </ul>
        </nav>
    </header>
    <div></div>
        <div>
            $html
        </div>

    </body>
    </html>
    END;
    }


}