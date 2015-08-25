<?php 

require_once("header.php"); 

if(isset($_GET['id']))
{
  $object = Section::get_by_id($_GET['id']);

  if($object == false || $object == null || $object == "")
  {
    header("location: index.php");
  }
  else
  {
    $batch = Batch::get_by_id($object->batchid);
    $school = School::get_by_id($batch->schoolid);
  }
}
else
{
  header("location: index.php?negative");
}

if(!$session->is_logged_in())
{
  header("location: index.php?negative");
}
else
{
  $user = User::get_by_id($session->user_id);

  if($user->enabled == DISABLED)
  {
    header("location: index.php?disabled");
  }

  if(
    !SchoolUser::amIAdmin($session->user_id, $object->schoolid) && 
    !BatchUser::amIAdmin($session->user_id, $object->batchid) && 
    !SectionUser::amIAdmin($session->user_id, $object->id) && 
    !$user->is_super_admin()
    )
  {
    header("location: index.php?negative");
  }
}

$pathinfo = pathinfo($_SERVER["PHP_SELF"]);
$basename = $pathinfo["basename"];
$currentFile = str_replace(".php","", $basename);

?>
<div class="container-fluid">
  <div class="row-fluid">
    <div class="span1"></div>
    <div class="span9">
      <form id="theform" class="form-horizontal" method="post" action="#" enctype="multipart/form-data">
        <fieldset>
        <legend>
          Update Section: <?php echo $object->name; ?>
        </legend>

        <div class="control-group">
          <label class="control-label" for="name">Batch</label>
          <div class="controls">
            <select name="batchselect" id="batchselect">
              <?php

              if(SchoolUser::amIAdmin($session->user_id, $object->schoolid) || $user->is_super_admin())
              {
                $batchs = Batch::get_all_by_schoolid($school->id);

                if(count($batchs) > 0)
                {
                  foreach ($batchs as $batch) 
                  {
                    if($batch->pending == 0 && $batch->enabled == 1)
                    {
                      echo "<option value='".$batch->id."'>".$batch->get_batchyear()."</option>";
                    }
                  }
                }
                else
                {
                  echo "<option value='0'>NO BATCHS YET</option>";
                }
              }
              else
              {
                $batchusers = BatchUser::getBatchsIAdminInSchool($session->user_id, $school->id);

                if(count($batchusers) > 0)
                {
                  foreach ($batchusers as $batchuser) 
                  {
                    $batch = Batch::get_by_id($batchuser->batchid);

                    if($batch->pending == 0 && $batch->enabled == 1)
                    {
                      echo "<option value='".$batch->id."'>".$batch->get_batchyear()."</option>";
                    }
                  }
                }
                else
                {
                  echo "<option value='0'>NO BATCHS YET</option>";
                }
              }

              ?>
            </select>
          </div>
        </div>

        <div class="control-group">
          <label class="control-label" for="name">Section Name</label>
          <div class="controls">
            <input value="<?php echo $object->name; ?>" id="name" name="name" type="text" placeholder="section name" class="input-xlarge">
          </div>
        </div>

        <div class="control-group">
          <label class="control-label" for="about">About</label>
          <div class="controls">                     
            <textarea id="about" name="about" class="span8" style="width:900px; height:200px"><?php echo $object->about; ?></textarea>
          </div>
        </div>

        <div class="control-group">
          <label class="control-label" for="about">Adviser's Message</label>
          <div class="controls">                     
            <textarea id="advisermessage" name="advisermessage" class="span8" style="width:900px; height:200px"><?php echo $object->advisermessage; ?></textarea>
          </div>
        </div>

        <div class="control-group">
          <label class="control-label" for="moto">Cover Photo</label>
          <div class="controls">
            <div class="fileupload fileupload-new" data-provides="fileupload">
            <div class="fileupload-new thumbnail" style="width: 800px; height: 300px;">
              <img src='data:image/jpeg;base64, <?php echo $object->picture; ?>' />
            </div>
              <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 800px; max-height: 300px; line-height: 20px;"></div>
              <div>
                <span class="btn btn-file">
                  <span class="fileupload-new">Select image</span>
                  <span class="fileupload-exists">Change</span>
                  <input name="MAX_FILE_SIZE" hidden value="2097152" />
                  <input name="cover" type="file" />
                </span>
                <button class="btn fileupload-exists" data-dismiss="fileupload">Remove</button>
                <a class="mytooltip" data-toggle="tooltip" data-placement="right" 
                  title=
                  "
                    OPTIONAL: extensions allowed: JPEG/JPG and PNG
                    , Up to 2MB, Recommended size: 800x300
                  ">
                  <span class="label label">?</span>
                </a>
              </div>
            </div>
          </div>
        </div>

        <div class="control-group">
          <label class="control-label" for="comments">SkooLyf Comments</label>
          <div class="controls">
            <input type="hidden" name="comments" value="<?php echo $object->comments; ?>" id="btn-input4" />
            <div class="btn-group" data-toggle="buttons-radio">
              <button type="button" value="1" id="btn-enabled4" class="btn <?php if($object->comments==1){echo'active';} ?>">Enabled</button>
              <button type="button" value="0" id="btn-disabled4" class="btn <?php if($object->comments==0){echo'active';} ?>">Disabled</button>
            </div>
          </div>
        </div>

        <div class="control-group">
          <label class="control-label" for="fbcomments">Facebook Comments</label>
          <div class="controls">
            <input type="hidden" name="fbcomments" value="<?php if($object->fbcomments==1){echo'1';}else{echo '0';} ?>" id="btn-input3" />
            <div class="btn-group" data-toggle="buttons-radio">
              <button type="button" value="1" id="btn-enabled3" class="btn <?php if($object->fbcomments==1){echo'active';} ?>">Enabled</button>
              <button type="button" value="0" id="btn-disabled3" class="btn <?php if($object->fbcomments==0){echo'active';} ?>">Disabled</button>
            </div>
          </div>
        </div>

        <div class="control-group">
          <label class="control-label" for="enabled">Access</label>
          <div class="controls">
            <input type="hidden" name="enabled" value="<?php if($object->enabled==1){echo'1';}else{echo '0';} ?>" id="btn-input2" />
            <div class="btn-group" data-toggle="buttons-radio">
              <button type="button" value="1" id="btn-enabled2" class="btn <?php if($object->enabled==1){echo'active';} ?>">Enabled</button>
              <button type="button" value="0" id="btn-disabled2" class="btn <?php if($object->enabled==0){echo'active';} ?>">Disabled</button>
            </div>
          </div>
        </div>
        
        <input type="hidden" name="sectionid" value="<?php echo $object->id; ?>" />

        <!-- Button -->
        <div class="control-group">
          <label class="control-label" for="btnsave"></label>
          <div class="controls">
            <button id="btnsave" name="btnsave" class="btn btn-primary">Save</button>
          </div>
        </div>

        </fieldset>
        </form>
    </div>
    <div class="span1"></div>
  </div><!--/row-->

  <script>

    $(function () 
    {

      $("#btnsave").click(function()
      {
        var formData = new FormData($("#theform")[0]);

        $("#btnsave").text("Saving...");
        $("#btnsave").attr("disabled", "disabled");

        $.ajax(
        {
          type: 'POST',
          url: 'includes/webservices/updatesection.php',
          data: formData,
          cache: false,
          contentType: false,
          processData: false,
          xhr: function() 
          {
              var myXhr = $.ajaxSettings.xhr();

              if(myXhr.upload)
              {
                  myXhr.upload.addEventListener('progress', progressHandlingFunction, false);
              }
              return myXhr;
          },
          success: function(result) 
          {
            if(result == "success")
            {
              showToast("Successfully Saved", "success");
              $("#btnsave").text("Save");
              $("#btnsave").removeAttr("disabled");
            }
            else
            {
              bootbox.alert(result);
              $("#btnsave").text("Save");
              $("#btnsave").removeAttr("disabled");
            }
          }
        });

        return false;
      });

      $(':file').change(function()
      {
          var file = this.files[0];
          name = file.name;
          size = file.size;
          type = file.type;
      });

      function progressHandlingFunction(e)
      {
        if(e.lengthComputable)
        {
          // $('.progress').attr({value:e.loaded,max:e.total});
          console.log("max: "+e.total+", progress: " + e.loaded);
        }
      }

      var btns2 = ['btn-enabled2', 'btn-disabled2'];
      var input2 = document.getElementById('btn-input2');

      for(var i = 0; i < btns2.length; i++) 
      {
        document.getElementById(btns2[i]).addEventListener('click', function() 
        {
          input2.value = this.value;
        });
      }

      var btns3 = ['btn-enabled3', 'btn-disabled3'];
      var input3 = document.getElementById('btn-input3');

      for(var i = 0; i < btns3.length; i++) 
      {
        document.getElementById(btns3[i]).addEventListener('click', function() 
        {
          input3.value = this.value;
        });
      }

      var btns4 = ['btn-enabled4', 'btn-disabled4'];
      var input4 = document.getElementById('btn-input4');

      for(var i = 0; i < btns4.length; i++) 
      {
        document.getElementById(btns4[i]).addEventListener('click', function() 
        {
          input4.value = this.value;
        });
      }

    });

</script>
    
<?php require_once("footer.php"); ?>