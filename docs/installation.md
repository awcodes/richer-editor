---
title: Installation
description: Install Richer Editor and import its CSS and views into your Filament theme.
---

# Installation

## Requiring the package

Install the package via Composer:

```bash
composer require awcodes/richer-editor
```

## Registering the styles

Richer Editor ships its own stylesheet as well as Blade views, and both need to reach your compiled CSS.

> [!IMPORTANT]
> If you have not set up a custom theme and are using Filament Panels, follow the instructions in the [Filament documentation](https://filamentphp.com/docs/5.x/styling/overview#creating-a-custom-theme) first.

Add both lines to your theme's CSS file — or your application's CSS file if you are using the standalone packages:

```css
@import '../../../../vendor/awcodes/richer-editor/resources/css/index.css';

@source '../../../../vendor/awcodes/richer-editor/resources/views/**/*.blade.php';
```

Adjust the relative paths if your CSS file does not live in the default theme location.

The `@import` is not optional if you use syntax-highlighted code blocks: the dark-mode switching for both the editor and rendered output is defined in that stylesheet. See [Code blocks](editor/code-blocks.md).

Nothing else needs registering — plugins are added per field, as shown in [Plugins](editor/plugins.md).
