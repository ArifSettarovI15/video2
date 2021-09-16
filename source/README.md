# TigerWeb Frontend
"Atomic Web Design" style

## Description

Technologies:

- NPM (package manager)
- Grunt (build system)
- Bower (package manager for the web)
- Git (vcs)
- SCSS(предпроцессор)

## Structure

```
source                      - корневой каталог разработки
└─bower_components          - каталог bower
 ├─build                    - билд собраный таск-менеджером
 ├─grunt                    - таски Grunt
 ├─node_modules             - Хуки для гита
 ├─node_modules             - node-плагины
 ├─src                      - каталог разработки
 │└─css                     - каталог разработки стилей
 ││ └─backend               - Админ часть
 ││ ├─frontend              - Фронтенд
 ││ └─app.scss              - Файл стиля
 │├─fonts                   - каталог шрифтов
 │├─images                  - каталог изображений
 ││ └─svg                   - SVG иконки 
 ││ └─static                - Статические картинки
 ││   └─icons               - Favicon и touch icons
 │├─js                      - каталог JS
 ││ └─libs                  - JS библиотеки
 ││ └─main                  - каталог разработки JS
 ││   └─backend             - Админ часть
 ││   ├─frontend            - Фронтенд
 ││   └─global              - Глобальные Js
 ││     └─func.js           - Глобальные функции
 ││     └─main.js           - Глобальные переменные и триггеры
 │├─modules                 - каталог модулей (JS + стили)
 │  └─module_name           - каталог модуля
 │    └─func.js             - JS функции модуля
 │    ├─main.js             - JS переменные и триггеры модуля
 │    └─style.scss          - стили модулы
 ├─.gitignore               - конфиг для Git
 ├─.htaccess                - конфиг для сервера
 ├─bower.js                 - конфиг Bower
 ├─Gruntfile.js             - конфиг Grunt
 ├─package.json             - конфиг пакетного менеджера npm
```

## Setup

Add Ssh key
```
ssh-add
```

Mark folders as Resources:

```bash
app/views
```

Mark folders as Exluded:

```bash
app/views/cache
assets
source/build
source/bower_components
```

Fork this repo:

```bash
git clone git@gitlab.tigerweb.ru:sheva/Frontend.git
```

Navigate to repo's root:

```bash
cd source
```

```bash
npm install
```

## Usage


Start grunt watch task:

```bash
grunt start
```

## BEM rools
1) Наименование: block-name__element-name_mod
2) Префиксы:
- **js-** (js триггеры) нельзя использовать для стилей


## Read more
- CSS style guide (http://cssguidelin.es/)
- SCSS (https://sass-guidelin.es/ru/)
- BEM (https://ru.bem.info/, http://frontender.info/MindBEMding/)
- OOCSS 
- Atomic design (https://habrahabr.ru/post/249223/)

## License

Apache License