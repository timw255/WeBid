
    	<div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12"> 
        <div class="x_panel">
                                <div class="x_title">
                                    <h2>{L_25_0018} <i class="fa fa-angle-double-right"></i> {L_5032} <i class="fa fa-angle-double-right"></i> {L_5031}</h2>
                                    <ul class="nav navbar-right panel_toolbox">
                                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                        <li><a class="close-link"><i class="fa fa-close"></i></a></li>
                                       <li><a class="close-link"><i class="fa fa-wrench"></i></a></li>
                                    </ul>
                                    <div class="clearfix"></div>
                                </div>
          <div class="col-md-12">
				<form name="errorlog" action="" method="post">
<!-- IF ERROR ne '' -->
					<div class="alert alert-success">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Success!</strong>{ERROR}</div>
<!-- ENDIF -->
                    <table class="table table-bordered table-striped">
                    <tr>
                        <td width="24%">{L_5034}</td>
                        <td width="76%">
                            <input type="text" name="name" class="form-control" maxlength="255" value="{NAME}">
                        </td>
                    </tr>
                    <tr>
                        <td>{L_5035}</td>
                        <td>
                            <p>{L_5036}</p>
                            <input type="text" name="msgstoshow" class="form-control" maxlength="4" value="{MSGTOSHOW}">
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>
                            <input type="radio" name="active" value="1"<!-- IF B_ACTIVE --> checked="checked"<!-- ENDIF -->> {L_5038}<br>
                            <input type="radio" name="active" value="2"<!-- IF B_DEACTIVE --> checked="checked"<!-- ENDIF -->> {L_5039}
                        </td>
                    </tr>
                    </table>
                    <input type="hidden" name="action" value="insert">
                    <input type="hidden" name="csrftoken" value="{_CSRFTOKEN}">
                    <input type="submit" name="act" class="btn btn-primary" value="{L_530}">
				</form>
            </div>
        </div>
      </div>
        </div>

<!-- INCLUDE footer.tpl -->
