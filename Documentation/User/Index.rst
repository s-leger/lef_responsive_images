.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _user-manual:

Users Manual
============


- How does it work?

  Well completely automagically, so there is nothing to do.


Settings
--------


Images layout
^^^^^^^^^^^^^

In the image/texpic content, Content Element Layout section of Appearance Tab there is a dropdown list "layout" providing two new options.

- **Circle** make the image round (by css)
- **Round corners** add round corners to pictures (depends on border radius settings in bootstrap style)


Loading method
^^^^^^^^^^^^^^

In the image/texpic content, Image Adjustments section of Appearance Tab there is a dropdown list "loading method" providing two options.

- **Lazyload** by default, only load picture when needed.
- **Preload** preload pictures right after page loading eg:on tabs.

.. tip::
   The rule is pretty simple : if you can't see your picture on any content set preload mode.

.. important::
   This setting is only effective when data-attribute image mode is set in constant editor.

