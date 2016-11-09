.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _developer:

Developer Corner
================

.. _developer-api:

TypoScript Reference
--------------------

Registers in use
^^^^^^^^^^^^^^^^

In order to compute the width of images, we are using five register values:

- **template_size.xxs** Width of layout < xs breakpoint
- **template_size.xs**  Width of layout between xs and sm breakpoint
- **template_size.sm**  Width of layout between sm and md breakpoint
- **template_size.md**  Width of layout between md and lg breakpoint
- **template_size.lg**  Width of layout >= lg breakpoint


Setting up fluid templates
^^^^^^^^^^^^^^^^^^^^^^^^^^

The number of columns of a template is given by variables in the templates, according bootstrap classes. Default to max number of columns when not set:

- xs
- sm
- md
- lg

You may also set margins

- marginxs
- marginsm
- marginmd
- marginlg

For full screen width content not wrapped into css class .container the variable fluid must be set to 1

::
  <f:cObject typoscriptObjectPath="lib.dynamicContent" data="{pageUid: '{data.uid}', colPos: '0', fluid: '1'}"/>
  
**Templates variables**
::
  <div class="col-md-9" role="main">
    <f:cObject typoscriptObjectPath="lib.dynamicContent" data="{pageUid: '{data.uid}', colPos: '0', md:'9'}"/>
  </div>
  <div class="col-md-3">
    <f:cObject typoscriptObjectPath="lib.dynamicContent" data="{pageUid: '{data.uid}', colPos: '2', md:'3'}"/>
  </div>

.. tip::
  When a breakpoint change default witdh by "col-breakpoint-numberOfColumns" class, all the breakpoints greater are influenced by this change. So if breakpoint class is col_md-9 we have to add md:9 only



lib.registerFrameSize
^^^^^^^^^^^^^^^^^^^^^^

This library provides a way to take account of content frames to compute image width. Any container modifying width of content should call this one before inserting content and clean up register after (using lib.restoreFrameSize).

**Implemented modifiers are :**

- indent 1/12
- indent 33/66
- indent 66/33
- well
- jumbotron


Adding customs modifiers with typoscript :
::
 


Implementation in image and textpic as reference:
::
  
Using on Gridelements is pretty simple :
::
 



Using gridelements columns layouts
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

As gridelements columns layouts change the width of your containers, a specially crafted version of the coulumns containers should be used to allow computing of the image width in any nested layout.
