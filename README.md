lef_responsive_images

A typo3 bootstrap_package extension to allow real image width generation for any breakpoint in fluid design

Currently, typo3 core only allow to set width of images without any layout relationship.

This ends up into providing images far larger than client would have needed (think about a picture in a 6 column layout rendered at 1200 / 600 / 300 px while only making 100px). Not only this consume bandwidth, but also energy to downsize images in the browsers.

Providing some care, it's possible to pre-compute real image size in nearly every layout for every breakpoint. Adding full support for correct img srcset and picture tag, and data-attributes lazyload solution, and css background.


**The cons :**

- Precomputing 5 to 10 images (breakpoints +1) can be time consuming on first load of the page.
- Configuration to take account of grid system, breakpoints, and content frames.
- Adapting templates to make system aware of layout.


**The pros :**

- Less bandwith
- Less energy
- Less time to load
- Automagic once configuration done, but with standards grids setups it should be ok right out of the box.


**Features :**

- Precompute real size and generate images according to the space available on any layout.
- Art direction images and css including carousel one
- Responsive Css backgrounds
- Allow from 1 up to 10 images per row
- Responsive carousel css bg and improved height setup (either via ratio or fixed per breakpoints)
- Support for pdf in frontend
- Html content allow to write inline css in header and js for footer (via layouts)
- Layout for multiple images regular, in row, in columns (including art direction)
- Textpic image in text left and right
- Support for user defined image width / height
- Allow to set image quality
- Image placeholder
- Support img with data-attributes, picture and img with srcset
- Prevent page relayout on image load via css

Why not ?
Who wants it ?
