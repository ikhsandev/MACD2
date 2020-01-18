<!DOCTYPE html>
<html>
<head>
    <title>IKHSAN - MACD Submission 2</title>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
<div class='header'><h2>IKHSAN - MACD <br>Azure Storage dan Azure Cognitive</h2></div>
<?php
  /**----------------------------------------------------------------------------------
  * Microsoft Developer & Platform Evangelism
  *
  * Copyright (c) Microsoft Corporation. All rights reserved.
  *
  * THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY KIND,
  * EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE IMPLIED WARRANTIES
  * OF MERCHANTABILITY AND/OR FITNESS FOR A PARTICULAR PURPOSE.
  *----------------------------------------------------------------------------------
  * The example companies, organizations, products, domain names,
  * e-mail addresses, logos, people, places, and events depicted
  * herein are fictitious.  No association with any real company,
  * organization, product, domain name, email address, logo, person,
  * places, or events is intended or should be inferred.
  *----------------------------------------------------------------------------------
  **/
  /** -------------------------------------------------------------
  # Azure Storage Blob Sample - Demonstrate how to use the Blob Storage service.
  # Blob storage stores unstructured data such as text, binary data, documents or media files.
  # Blobs can be accessed from anywhere in the world via HTTP or HTTPS.
  #
  # Documentation References:
  #  - Associated Article - https://docs.microsoft.com/en-us/azure/storage/blobs/storage-quickstart-blobs-php
  #  - What is a Storage Account - http://azure.microsoft.com/en-us/documentation/articles/storage-whatis-account/
  #  - Getting Started with Blobs - https://azure.microsoft.com/en-us/documentation/articles/storage-php-how-to-use-blobs/
  #  - Blob Service Concepts - http://msdn.microsoft.com/en-us/library/dd179376.aspx
  #  - Blob Service REST API - http://msdn.microsoft.com/en-us/library/dd135733.aspx
  #  - Blob Service PHP API - https://github.com/Azure/azure-storage-php
  #  - Storage Emulator - http://azure.microsoft.com/en-us/documentation/articles/storage-use-emulator/
  #
  **/
  require_once 'vendor/autoload.php';
  use MicrosoftAzure\Storage\Blob\BlobRestProxy;
  use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
  use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
  use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
  use MicrosoftAzure\Storage\Blob\Models\CreateBlobOptions;
  use MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions;
  use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

  $connectionString = "DefaultEndpointsProtocol=https;AccountName=ikhsanmacd2;AccountKey=WKTrwbupXm5uMNmj2uBq9ycNhv5WylBHeXBflitqKEEI/RONUL1Fe9S3H2DllK6XvMDVsoS23DnYPbjlUmAE+A==;EndpointSuffix=core.windows.net";


// Create blob client.
$blobClient = BlobRestProxy::createBlobService($connectionString);

$fileToUpload = "ikhsan.txt";
$ip_address = $_SERVER['REMOTE_ADDR'];
$containerName = "blockblobs". md5($ip_address);

$result = $blobClient->listContainers();
$containers = $result->getContainers();
$list_container_names = array_map(function ($container) {
    return $container->getName();
}, $containers);

// check container exist or not
if (!in_array($containerName, $list_container_names)) {

    try {
        // Create container options object.
        $createContainerOptions = new CreateContainerOptions();

        // Set public access policy. Possible values are
        // PublicAccessType::CONTAINER_AND_BLOBS and PublicAccessType::BLOBS_ONLY.
        // CONTAINER_AND_BLOBS:
        // Specifies full public read access for container and blob data.
        // proxys can enumerate blobs within the container via anonymous
        // request, but cannot enumerate containers within the storage account.
        //
        // BLOBS_ONLY:
        // Specifies public read access for blobs. Blob data within this
        // container can be read via anonymous request, but container data is not
        // available. proxys cannot enumerate blobs within the container via
        // anonymous request.
        // If this value is not specified in the request, container data is
        // private to the account owner.
        $createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);

        // Set container metadata.
        $createContainerOptions->addMetaData("key1", "value1");
        $createContainerOptions->addMetaData("key2", "value2");

        // Create container.
        $blobClient->createContainer($containerName, $createContainerOptions);
    }
    catch(ServiceException $e){
        // Handle exception based on error codes and messages.
        // Error codes and messages are here:
        // http://msdn.microsoft.com/library/azure/dd179439.aspx
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.":A ".$error_message."<br />";
    }
    catch(InvalidArgumentTypeException $e){
        // Handle exception based on error codes and messages.
        // Error codes and messages are here:
        // http://msdn.microsoft.com/library/azure/dd179439.aspx
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.":B ".$error_message."<br />";
    }
}

// List blobs.
$listBlobsOptions = new ListBlobsOptions();

  if (isset($_POST["Upload"])) {
    $i=1;
      $errors= array();
      $file_name = $_FILES['fileToUpload']['name'];
      $file_size =$_FILES['fileToUpload']['size'];
      $file_tmp =$_FILES['fileToUpload']['tmp_name'];
      $file_ext=strtolower(pathinfo($_FILES['fileToUpload']['name'], PATHINFO_EXTENSION));

      $extensions= array("jpeg","jpg","png");

      if(!$file_name){
        $errors[]="Please select image to upload.";
      }

      if($file_name && in_array($file_ext,$extensions)=== false){
         $errors[]="Extension not allowed, please choose a JPEG file.";
      }

      if($file_size > 2097152){
         $errors[]='File size must be less than 2 MB';
      }

      if(empty($errors)==true){
         try {
             // Getting local file so that we can upload it to Azure
             $content = fopen($file_tmp, "r");
             try {
                 //Upload blob
                 $blobClient->createBlockBlob($containerName, $file_name, $content);
                 echo "<div class='box'><div class='input-group'>File has successfully uploaded</div></div>";

             } catch(ServiceException $e){
                 $code = $e->getCode();
                 $error_message = $e->getMessage();
                 echo $code.": ".$error_message."<br />";
             }
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
      }else{
        echo "<div class='box'><div class='input-group'>";
        foreach ($errors as $error)
        {
          echo $error."<br />";
        }
        echo "</div></div>";
      }
      }
?>

<script type="text/javascript">
    function processImage(i) {
        var subscriptionKey = "d948dd0db7944ed5ba62e3fb327407da";

        var uriBase =
            "https://southeastasia.api.cognitive.microsoft.com/vision/v2.0/analyze";

        // Request parameters.
        var params = {
            "visualFeatures": "Categories,Description,Color",
            "language": "en",
        };

        // Display the image.
        var sourceImageUrl = document.getElementById("inputImage" + i).value;
        document.querySelector("#sourceImage").src = sourceImageUrl;
        $("#responseTextArea").val("");
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
            $("#responseTextArea2").val(JSON.stringify(data, null, 2));
          


            // Show formatted JSON on webpage.
            var output = JSON.stringify(data, null, 2);
            var parsedOutput = JSON.parse(output);
            $("#responseTextArea").val(parsedOutput.description.captions[0].text);
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
<center><form method="post" action="index.php" enctype="multipart/form-data">
  <div class="input-group">
    Pilih Image Untuk Di Upload:
    <input type="file" name="fileToUpload" accept="image/*" id="fileToUpload">
  </div>
  <div class="input-group">
    <button type="submit" class="btn" name="Upload">Upload</button>
    <button type="submit" class="btn" name="load_data">Tampilkan Data</button>
      </div>
</form>
<form method="post" action="phpQS.php?Cleanup&containerName=<?php echo $containerName; ?>">
    <button type="submit" class="btn">Hapus Data Container</button></form></center>

<?php
if(isset($_POST["load_data"])){
    try {    // List blobs.
        $listBlobsOptions = new ListBlobsOptions();
        $i=1;
                //$listBlobsOptions->setPrefix("ikhsanmacd2"); 
        //<?php echo sizeof($result->getBlobs())
        echo "<center><div class='box' style='background : #250f52; color : white;'><div class='input-group'><b>Daftar Data</b></div></div></center>";
        echo "<div class='box'><div class='input-group'><table>";
        echo "<tr><th>Image Name</th>";
        echo "<th>Image Url</th>";
        echo "<th>Action</th></tr>";
        do{
            $result = $blobClient->listBlobs($containerName, $listBlobsOptions);
            foreach ($result->getBlobs() as $blob)
            {
              echo "<tr><td>".$blob->getName()."</td>";
              echo "<td>".$blob->getUrl()."</td>";
              echo "<td><input type='text' name='inputImage' id='inputImage".$i."' value='".$blob->getUrl()."' hidden/>
              <button onclick='processImage(".$i.")'>Analyze Image</button></td></tr>";
              $i++;
            }
            echo "</table></div></div>";
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
    echo "<center><div class='box'>
<div id='wrapper' style='width:800px; display:table;''>
    <div id='imageDiv' style='width:420px; display:table-cell;'>
        Image Analysis Result:
        <br><textarea id='responseTextArea' class='UIInput'
                  style='width:400px; height:50px;' disabled></textarea><br>
        <img id='sourceImage' width='400' />
    </div>
  <div id='jsonOutput' style='width:420px; display:table-cell;'>
    Response :
    <br><textarea id='responseTextArea2' class='UIInput'
    style='width:400px; height:400px;' readonly=''></textarea>
  </div>
</div></center>";
}
?>
<div class="footer">
  <p>&copy; Copyright <script>document.write(new Date().getFullYear())</script> | Ikhsan Andriyawan</p>
</div>
</body>
</html>
