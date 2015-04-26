lef_responsive_images
A typo3 bootstrap_package extension to allow real image width generation for any breakpoint in fluid design

**What does it do ?**

Current settings in typo3 lead to images size driving your design. We think that design should drive images sizes.

Extends typo3 6.2-7.0+ bootstrap_package

- Keep image / text proportions in any layout (including nested) for all breakpoints.
- Automatically pre-compute real image size for each breakpoint and generate images according.
- Use advanced javascript lazyload solution to load the right source on each target device.
- Enhance support for responsive <picture><srcset> and <img sizes="" srcset=""> tags
- Add optional support for retina.


**In depth, what does it solve, why and how ?**

Well, it's all about proportions.

Setting a width of picture or a default width for breakpoint is far not enougth to ensure stability of proportions between text / images. In a layout with say 2 columns (8/4), the image width is the same for both columns. And things may even go wronger with gridelements nested columns.

Without knowledge of layout, typo3 core is not able to handle such situations. So in order for the design to take control over image sizes, we do have to handle the problem from layout perspective, and drive images sizes according.
