.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _introduction:

Introduction
============


.. _what-it-does:

What does it do?
----------------

Responsive design is great, but providing the right image size to any device is a challenging task.

This extension of the Bootstrap Package solve this problem by pre-computing the real size of images for each "breakpoint" on any layout.
Taking care of the delivery of the right image using any modern <picture> <srcset> or javascript based data-attribute solution using media query.

- <img srcset> in size="" tag with screen width percentage and breakpoints media query, and generated image width in source="" tag.
- <picture> with appropriate media queries screen size and pixel density attributes.
- with custom advanced lazy-load javascript solution using data-attributes.

Out of the box, the settings are matching bootstrap's default.

Features
^^^^^^^^

- Art direction
- Allow from 1 to 10 columns of images
- User defined image sizes
- Multi-images layout in regular grid, no-rows, no-cols
- Css responsive background
- Textpic with images in text left and right
- Extended carousel (multiple images, responsive background and enhanced height setup)
- Prevent relayout on images load via css
- Images placeholder
- Image quality management


Browser Compatibility
^^^^^^^^^^^^^^^^^^^^^

IE 9+
Edge
Chrome
Firefox
Safari
