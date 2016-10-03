
<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
      <h2>{L_5142} <i class="fa fa-angle-double-right"></i> {L_075}</h2>
        <ul class="nav navbar-right panel_toolbox">
          <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
          <li><a class="close-link"><i class="fa fa-close"></i></a></li>
          <li><a class="close-link"><i class="fa fa-wrench"></i></a></li>
        </ul>
        <div class="clearfix"></div>
      </div>
      <div class="col-md-12">
				<form name="payments" action="" method="post">
<!-- IF ERROR ne '' -->
					 <div class="alert alert-success"> <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> <strong>Success!</strong>{ERROR}</div>
<!-- ENDIF -->
					<div class="plain-box">{L_092}</div>
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>&nbsp;</th>
                            <th><b>{L_087}</b></th>
                            <th><b>{L_008}</b></th>
                        </tr>
<!-- BEGIN payments -->
                        <tr>
                            <td>&nbsp;</td>
                            <td>
                                <input type="text" name="new_payments[]" value="{payments.PAYMENT}" class="form-control">
                            </td>
                            <td align="center">
                                <input type="checkbox" name="delete[]" value="{payments.S_ROW_COUNT}">
                            </td>
                        </tr>
<!-- END payments -->
                        <tr>
                            <td colspan="2" align="right">{L_30_0102}</td>
                            <td align="center"><input type="checkbox" class="selectall" value="delete" class="form-control"></td>
                        </tr>
                        <tr>
                            <td>{L_394}</td>
                            <td>
                                <input type="text" name="new_payments[]" class="form-control">
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="csrftoken" value="{_CSRFTOKEN}">
                    <input type="submit" name="act" class="btn btn-primary" value="{L_089}">
				</form>
            </div>
        </div>
        </div>
        </div>
<!-- INCLUDE footer.tpl -->