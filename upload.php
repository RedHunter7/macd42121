<?php
require_once 'try-blob-storage/vendor/autoload.php';
require_once "try-blob-storage/random_string.php";

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

# Mengatur instance dari Azure::Storage::Client
$connectionString ="DefaultEndpointsProtocol=https;AccountName=macd42121;AccountKey=oIQUp6TVtKpLy/iIYu+l/4/eTItFjV9MnnBscnImUmZYZrWRL+eztzZzQyXq6w4dD2LtB4wcCu8fgNSzsT3qqg==;EndpointSuffix=core.windows.net";
 
// Membuat blob client.
$blobClient = BlobRestProxy::createBlobService($connectionString);

if(isset($_POST['upload']))
{
    $fileToUpload = $_FILES["img"]["name"];
 
    # Membuat BlobService yang merepresentasikan Blob service untuk storage account
    $createContainerOptions = new CreateContainerOptions();
 
    $createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);
 
    // Menetapkan metadata dari container.
    $createContainerOptions->addMetaData("key1", "value1");
    $createContainerOptions->addMetaData("key2", "value2");
 
    $containerName = "blockblobs".generateRandomString();

    // Create container.
    $blobClient->createContainer($containerName, $createContainerOptions);
    
 
    // Sampai kode di atas kita telah membuat instancce Azure storage client, menginstansiasi objek blob service, membuat container baru, dan mengatur perijinan ke container agar blob bisa diakses oleh semua.
    
    $content = fopen($_FILES['img']['tmp_name'], "r");
    //Mengunggah blob
    $blobClient->createBlockBlob($containerName, $fileToUpload, $content);

    $listBlobsOptions = new ListBlobsOptions();
    $listBlobsOptions->setPrefix("");
    $result = $blobClient->listBlobs($containerName, $listBlobsOptions);

    do{
       foreach ($result->getBlobs() as $blob)
       {
          $imgUrl = $blob->getUrl();
       }
 
       $listBlobsOptions->setContinuationToken($result->getContinuationToken());
      } while($result->getContinuationToken());

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Upload Image</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
</head>
<body>
<h1>Upload Image</h1>
    <form action="" method="post" enctype="multipart/form-data">
      <input type="file" name="img" id=""> <br><br>
      <button type="submit" name="upload">Upload</button>
    </form>
    <br>
    <button onclick="processImage();">analyze image</button>

    <br>
    <br>
    <br>
    <br>
    <br>

<div style="display:flex;">
<?php if(isset($_POST['upload'])) 
{   ?>
    <img src="<?php echo $imgUrl; ?>" alt="" id="img" style="width:50%;">
<?php } ?>
<div id="jsonOutput" style="width:50%; display:table-cell;">
        Response:
        <br><br>
        <textarea id="responseTextArea" class="UIInput"
                  style="width:580px; height:400px;"></textarea>
</div>

</div>

<script type="text/javascript">
    function processImage() {
        // **********************************************
        // *** Update or verify the following values. ***
        // **********************************************
 
        // Replace <Subscription Key> with your valid subscription key.
        var subscriptionKey = "0fe1760638b54fbba01ea429f08e061a";
 
        // You must use the same Azure region in your REST API method as you used to
        // get your subscription keys. For example, if you got your subscription keys
        // from the West US region, replace "westcentralus" in the URL
        // below with "westus".
        //
        // Free trial subscription keys are generated in the "westus" region.
        // If you use a free trial subscription key, you shouldn't need to change
        // this region.
        var uriBase =
            "https://southeastasia.api.cognitive.microsoft.com/vision/v2.0/analyze";
 
        // Request parameters.
        var params = {
            "visualFeatures": "Categories,Description,Color",
            "details": "",
            "language": "en",
        };
 
        // Display the image.
        var sourceImageUrl = "<?php echo $imgUrl; ?>";
 
        // Make the REST API call.
        $.ajax({
            url: uriBase + "?" + $.param(params),
 
            // Request headers.
            beforeSend: function(xhrObj){
                xhrObj.setRequestHeader("Content-Type","application/json");
                xhrObj.setRequestHeader(
                    "Ocp-Apim-Subscription-Key", subscriptionKey);
            },
 
            type: "POST",
 
            // Request body.
            data: '{"url": ' + '"' + sourceImageUrl + '"}',
        })
 
        .done(function(data) {
            // Show formatted JSON on webpage.
            $("#responseTextArea").val(JSON.stringify(data, null, 2));
        })
 
        .fail(function(jqXHR, textStatus, errorThrown) {
            // Display error message.
            var errorString = (errorThrown === "") ? "Error. " :
                errorThrown + " (" + jqXHR.status + "): ";
            errorString += (jqXHR.responseText === "") ? "" :
                jQuery.parseJSON(jqXHR.responseText).message;
            alert(errorString);
        });
    };
</script>
</body>
</html>