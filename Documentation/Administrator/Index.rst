.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _admin-manual:

Administrator Manual
====================

.. _admin-installation:

Installation
------------

- The backend layouts of Bootstrap Package (bootstrap_package) must be loaded
- The static template of Bootstrap Package (bootstrap_package) must be loaded before this one

To install the extension, perform the following steps:

#. Go to the Extension Manager
#. Install the extension
#. Load the static template after Bootstrap Package (bootstrap_package)
#. ...


Extension manager configuration
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

- **Disable TCEFORM** Disable loading of TCEFORM adding two image layouts for round and round border class to images


Note on Breakpoints
^^^^^^^^^^^^^^^^^^^

When changing default breakpoints, you have to modify the settings of resposiveimages.min.js initialisation in main.js according.

.. code-block:: javascript
   :linenos:
   :emphasize-lines: 6-10

	$(document).ready(
		function () {
			$(".lazyload").responsiveimages({
				threshold:100,     // load image when 100 pixel near screen
				breakpoints:{
					0:xsmall,
					480:small,
					768:medium,
					992:large,
					1200:bigger
				},
				retina:0,           // 1 to enable retina
				scrolldirection:'y' // scroll direction can be x, y, xy, default:y
			});
		}
	);


.. _admin-configuration:

Configuration
-------------

Constants in Typoscript constants editor
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^


**Breakpoints media query**

Breakponits **screen** width, according your bootstrap settings.

- **Screen lg** Minimum width of the screen large  default:1200
- **Screen md** Minimum width of the screen medium default:992
- **Screen sm** Minimum width of the screen small  default:768
- **Screen xs** Minimum width of the screen extra small default:480


**Retina settigs**

- **Enable retina** Enable retina images in source sets default:false
- **Retina Pixel density** Pixel density for retina images default:2
- **Retina density media query** Media query tag for pixel density default:-webkit-min-device-pixel-ratio


**Image width**

This is the base width of **containers** used to compute images width, according your bootstrap settings.

- **Container lg** Width of the container large default:1140
- **Container md** Width of the container medium default:940
- **Container sm** Width of the container small default:720
- **Container xs** Width of the container xtra small default:450


**Columns**

- **Number of columns** Maximum number of columns default:12


**Sizes**

- **Gutter width** Width of the gutter default:30
- **Padding jumbotron** Width of jumbotron padding default:60
- **Padding well** Width of the well padding default:40


**Layout**

- **Path to favicon** Path to favicon image default:EXT:lef_responsive_images/Resources/Public/Icons/favicon.ico
- **Rendering-type for responsive image** Tag used to render responsive images. default:img-tag with alternate sources as data-attributes

  - Default img-tag : completely disable responsive features
  - img-tag with alternate sources as srcset-attribute
  - picture-tag with source-child-tags
  - img-tag with alternate sources as data-attributes : enable javascript lazy loading feature

**Features**

- **Path to javascript** Path to Javascript plugins default:EXT:lef_responsive_images/Resources/Public/JavaScript/
- **Path to Css** Path to Css files default: EXT:lef_responsive_images/Resources/Public/Css/



.. _admin-faq:

FAQ
---

How many images are generated ?
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

**By default, Bootstrap define 4 breakpoints.**

| The first (xs) is fluid, the others are fixed.
| In order to take account of those 4 breakpoints, we need 5 different images sizes.
|

**Breakpoints**

+-----------+------------+----------+----------+
|Layout     |Images      |Breakpoint|Width     |
+===========+============+==========+==========+
|fluid      |1           |0         |  0 - xs-1|
+-----------+------------+----------+----------+
|fluid      |2           |xs        | xs - sm-1|
+-----------+------------+----------+----------+
|fixed      |3           |sm        | sm - md-1|
+-----------+------------+----------+----------+
|fixed      |4           |md        | md - lg-1|
+-----------+------------+----------+----------+
|fixed      |5           |lg        | lg+      |
+-----------+------------+----------+----------+
