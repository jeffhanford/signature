Basic instructions on how the system works (work in progress)

signature.php is the user facing php/html file, it gathers the information input by the player and calls update.php when the button is pressed
update.php adds players to the database and creates and updates their signatures. It is also run by cron hourly to update the signatures
Updatesignature.php contains functions that involve creating signature images
WGAPI.php contains functions used to access databases
colorscales.php contains the colorscale ranking values
gif.php is used to compose and decompose gifs
GIFEncoder and GifFrameExtractor were created by other developers and are the guts of the gif composition and decomposition
