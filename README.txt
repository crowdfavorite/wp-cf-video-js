CF VideoJS
----------

An easy-to-use plugin for enabling HTML5 video on your site. HTML5 video is a great way to serve up cross-platform video for any device, including the iPad and iPhone.

#### Embedding Videos

After installing the plugin, embedding videos is easy. In the post editor, you can use the shortcode:

	[video]

This won't do much without some attributes, however:

	[video src="http://example.com/video.mp4 http://example.com/video.ogg"]

The src parameter is a url to your video file. Notice you can include multiple file encodings by separating their URLs with a space. The browser will choose whichever one it understands.

##### Available Attributes

- `src`: Required. a space-separated list of valid, full urls to video files. You should make sure one of the URLs is an mp4 if you want Flash fallbacks to work (highly recommended). Including multiple encodings means more browsers can use native playback controls. Don't worry if all you have is an mp4, however: browsers can always use the Flash fallback.
- `width`: Optional. The desired width of the video. Defaults to 640px (the width of a YouTube video).
- `height`: Optional. The desired height of the video. Defaults to 360px (the height of a YouTube video).
- `poster`: Optional. Full url to a poster image that can be shown for the video while it loads (recommended).
- `id`: Optional. Adds a unique HTML ID to the video tag.
- `overajax`: Optional. Set to `true` if the video is going to be loaded over AJAX. This adds a registration script to the video after the initial body load has finished.