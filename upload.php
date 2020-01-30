<?php
require_once 'try-blob-storage/vendor/autoload.php';
require_once "try-blob-storage/random_string.php";

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

if(isset($_POST['upload']))
{
    # Mengatur instance dari Azure::Storage::Client
    $connectionString = "DefaultEndpointsProtocol=https;AccountName=".getenv('account_name').";AccountKey=".getenv('account_key');
 
    // Membuat blob client.
    $blobClient = BlobRestProxy::createBlobService($connectionString);
    $fileToUpload = $_FILES["img"]["name"];
 
    # Membuat BlobService yang merepresentasikan Blob service untuk storage account
    $createContainerOptions = new CreateContainerOptions();
 
    $createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);
 
    // Menetapkan metadata dari container.
    $createContainerOptions->addMetaData("key1", "value1");
    $createContainerOptions->addMetaData("key2", "value2");
 
    $containerName = "blockblobs".generateRandomString();
 
    try    {
        // Membuat container.
        $blobClient->createContainer($containerName, $createContainerOptions);
 
 
    // Sampai kode di atas kita telah membuat instancce Azure storage client, menginstansiasi objek blob service, membuat container baru, dan mengatur perijinan ke container agar blob bisa diakses oleh semua.
    
    $content = fopen($_FILES['img']['tmp_name'], "r");
    //Mengunggah blob
    $blobClient->createBlockBlob($containerName, $fileToUpload, $content);

    $listBlobsOptions = new ListBlobsOptions();
 
    do{
       $result = $blobClient->listBlobs($containerName, $listBlobsOptions);
       foreach ($result->getBlobs() as $blob)
       {
          $imgUrl = $blob->getUrl();
       }
 
       $listBlobsOptions->setContinuationToken($result->getContinuationToken());
      } while($result->getContinuationToken());
    } 
    catch(ServiceException $e){
        // Handle exception based on error codes and messages.
        // Error codes and messages are here:
        // http://msdn.microsoft.com/library/azure/dd179439.aspx
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message."<br />";
    }
    catch(InvalidArgumentTypeException $e){
        // Handle exception based on error codes and messages.
        // Error codes and messages are here:
        // http://msdn.microsoft.com/library/azure/dd179439.aspx
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message."<br />";
    }

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Upload Image</title>
</head>
<body>
<h1>Upload Image</h1>
    <form action="" method="post" enctype="multipart/form-data">
      <input type="file" name="img" id=""> <br><br>
      <button type="submit" name="upload">Upload</button>
    </form>

    <br>
    <br>
    <br>
    <br>
    <br>

<?php if(isset($_POST['upload'])) 
{   ?>
    <img src="<?php echo $imgUrl; ?>" alt="">
<?php } ?>
</body>
</html>