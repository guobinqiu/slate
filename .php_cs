
<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->name('/\.php$/')
    ->notName('/\.yml$/')
    ->notName('/\.twig$/')
    ->notName('/\.sh$/')
    ->in(__DIR__.DIRECTORY_SEPARATOR. 'src');

#    ->notPath('Jili/EmarBundle/Api2/Utils')
#    ->notPath('Jili/EmarBundle/Api2/Request')

return Symfony\CS\Config\Config::create()
->finder($finder)
;

