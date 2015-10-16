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

- **width_xxs** Width of layout < xs breakpoint
- **width_xs**  Width of layout between xs and sm breakpoint
- **width_sm**  Width of layout between sm and md breakpoint
- **width_md**  Width of layout between md and lg breakpoint
- **width_lg**  Width of layout >= lg breakpoint


Setting up fluid templates
^^^^^^^^^^^^^^^^^^^^^^^^^^

The number of columns of a template is given by variables in the templates, according bootstrap classes. Default to max number of columns when not set:

- col_xs
- col_sm
- col_md
- col_lg

**Templates variables**
::
  <div class="col-md-9" role="main">
    <f:cObject typoscriptObjectPath="lib.dynamicContent" data="{pageUid: '{data.uid}', colPos: '0', col_md:'9', col_lg:'9'}"/>
  </div>
  <div class="col-md-3">
    <f:cObject typoscriptObjectPath="lib.dynamicContent" data="{pageUid: '{data.uid}', colPos: '2', col_md:'3', col_lg:'3'}"/>
  </div>

.. tip::
  When a breakpoint change default witdh by "col-breakpoint-numberOfColumns" class, all the breakpoints greater are influenced by this change. So if breakpoint class is col_md-9 we have to add both col_md:9 and col_lg:9



lib.registerFrameSize
^^^^^^^^^^^^^^^^^^^^^^

This library provides a way to take account of content frames to compute image width. Any container modifying width of content should call this one before inserting content and clean up register after (using lib.restoreFrameSize).

**Implemented modifiers are :**

- indent 1/12
- indent 33/66
- indent 66/33
- well
- jumbotron
- bootstrap_package panel


Adding customs modifiers with typoscript :
::
  lib.registerFrameSize {
    # width modifier in xxs layout
    width_xxs{
      cObject{
        # bootstrap panel implementation as reference
        30 = TEXT
        30{
          value = -2
          stdWrap.if{
            value = bootstrap_package_panel
            equals.field = CType
          }
        }
        # Your own modifier
        50 = TEXT
        50{
          # the width of the frame of the custom element (on both sides)
          # -2 for a 1 pixel border in both sides same for padding, margins..
          value = -2
          stdWrap.if {
            value = 2
            equals.field = layout
          }
        }
      }
    }

    # width modifier in xs layout
    width_xs.cObject.50.value = -5

    # width modifier in sm layout omitted if the same as xs
    # width_sm.cObject.50.value = -5

    # width modifier in md layout only when change
    width_md.cObject.50.value = -8

    # width modifier in lg layout only when change
    width_lg.cObject.50.value = -10
  }


  lib.restoreFrameSize = RESTORE_REGISTER


Implementation in image and textpic as reference:
::
  tt_content.image.5    = < lib.registerFrameSize
  tt_content.image.30   = < lib.restoreFrameSize
  tt_content.textpic.5  = < lib.registerFrameSize
  tt_content.texpic.30  = < lib.restoreFrameSize

Using on Gridelements is pretty simple :
::
  tt_content.gridelements_pi1.20{
     5 = < lib.registerFrameSize
     10.setup {
     ...
     }
     15 = < lib.restoreFrameSize
  }



Using gridelements columns layouts
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

As gridelements columns layouts change the width of your containers, a specially crafted version of the coulumns containers should be used to allow computing of the image width in any nested layout.
