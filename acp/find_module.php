<!-- INCLUDE overall_header.html -->

<style>
.icon
{
	box-shadow: 0px 0px 0px 1px #FFF inset;
	white-space: nowrap;
	border-radius: 4px;
	font-size: 1.2em;
	width: 16px;
	padding: 5px;
	display: inline-block;
	border: 1px solid #C7C3BF;
	background-color: #FFFFFF;
	color: #BC2A4D;
}

.stop
{
	list-style: outside none none;
	margin: 0px 0px 0px -3px;
	line-height: 0.9em;
}
.em_spam::before {
	content: "\f12a";
	font-size: 1.2em;
}
.spam::before {
	content: "\f071";
	color: #BC2A4D;
	font-size: 1.2em;
}
.ip::before {
	content: "\f129";
	color: #BC2A4D;
	font-size: 1.2em;
}
.find::before {
	content: "\f007";
	color: #228822;
	font-size: 1.2em;
}
</style>

<a name="maincontent"></a>

<h1>{L_ACP_FIND_SPAMER}</h1>
<p>{L_ACP_FIND_SPAMER_EXPLAIN}</p>

<form name="acp" method="post" action="{S_ACTION}">
	<!-- IF .row --><span style="font-size: 80%;">{EXEC_TIME}</span>
	<!-- IF .pagination -->
	<div class="pagination">
		{TOTAL_USERS}
		<!-- IF .pagination -->
			<!-- INCLUDE pagination.html -->
		<!-- ELSE -->
			 &bull; {PAGE_NUMBER}
		<!-- ENDIF -->
	</div>
	<!-- ENDIF -->
	<table class="table1 zebra-table fixed-width-table responsive">
	<thead>
		<tr>
			<th width="18%">{L_USER_NAME}</th>
			<th width="25%">{L_USER_EMAIL}</th>
			<th width="18%">{L_JOINED}</th>
			<th width="22%">{L_LAST_VISIT}</th>
			<th width="11%">{L_POSTS}</th>
			<th width="14%">IP</th>
			<th width="3%">IP</th>
			<th width="6%">e-mail</th>
			<th width="5%">{L_NAME}</th>
			<th width="8%">{L_RESUME}</th>
			<th width="9%">{L_MARK}</th>
		</tr>
	</thead>
	<tbody>
		<!-- BEGIN row -->
		<tr<!-- IF row.S_FAIL_CHK --> style="background-color: #FCDEC0;"<!-- ENDIF -->>
			<td>{row.USER_NAME}</td>
			<td>{row.USER_EMAIL}</td>
			<td>{row.USER_REG_DATE}</td>
			<td>{row.LAST_VISIT}</td>
			<td><!-- IF row.USER_POSTS --><a href="{row.U_POSTS}">{row.USER_POSTS}</a><!-- ELSE -->{row.USER_POSTS}<!-- ENDIF --></td>
			<td><!-- IF row.S_USER_IP --><a href="{row.S_USER_IP}" onclick="popup(this.href, 700, 500); return false;">{row.USER_IP}</a><!-- ELSE -->{row.USER_IP}<!-- ENDIF --></td>
			<td style="text-align:center"><!-- IF row.IP_IMG and row.S_IP_FIND --><ul class="stop"><li><i class="icon {row.IP_IMG} fa-fw" aria-hidden="true"></i><!-- ENDIF --></td>
			<td style="text-align:center"><!-- IF row.SPAM_MAIL --><ul class="stop"><li><i class="icon fa-check fa-fw" aria-hidden="true"></i><!-- ENDIF --></td>
			<td style="text-align:center"><!-- IF row.SPAM_NICK --><ul class="stop"><li><i class="icon fa-check fa-fw" aria-hidden="true"></i><!-- ENDIF --></td>
			<td style="text-align:center"><a href="{row.U_FULL_CHECK}" onClick="popup(this.href, 650, 500, ''); return false;"><i class="icon {row.CLASS} fa-fw" aria-hidden="true"></i></a></td>
			<td style="text-align:center"><!-- IF row.IS_FIND --><input name="id_list[]" type="checkbox" class="radio" value="{row.USER_ID}" /><!-- ENDIF --></td>
		</tr>
		<!-- END row -->
	</tbody>
	</table>

	<!-- IF .pagination -->
	<div class="pagination">
		{TOTAL_USERS}
		<!-- IF .pagination -->
			<!-- INCLUDE pagination.html -->
		<!-- ELSE -->
			 &bull; {PAGE_NUMBER}
		<!-- ENDIF -->
	</div>
	<!-- ENDIF -->

	<fieldset class="quick">
		<div class="errorbox" style="padding-top:8px">{L_WARNING_MESSAGE}</div>
		<input class="button2" name="delmarked" value="{L_DELETE_SELECTED}" type="submit"><br>
		<p class="small"><a href="#" onClick="marklist('acp', '', true); return false;">{L_MARK_ALL}</a> :: <a href="#" onClick="marklist('acp', '', false); return false;">{L_UNMARK_ALL}</a></p>
	</fieldset>
	<!-- ELSE -->
	<p>{L_NOT_FIND}</p>
	<!-- ENDIF -->
	<fieldset style="margin: 0px;" class="display-options">
		{L_SEARCH_OPTION}:&nbsp;<select name="f" onchange="if(this.options[this.selectedIndex].value != -1){ document.forms['acp'].submit() }">
		<!-- BEGIN jumpbox_options -->
		<option value="{jumpbox_options.OPTION_ID}"{jumpbox_options.SELECTED}>{jumpbox_options.OPTION}</option>
		<!-- END jumpbox_options -->
		</select>&nbsp;&nbsp;{L_FILTER}&nbsp;
		<select name="f_opt">{FILTER_OPTIONS}</select>
		&nbsp;<input name="filter" type="text" value="{FILTER}" size="20" maxlength="20">
		&nbsp;{L_SELECT_SORT}:&nbsp;
		<select name="sk">{S_MODE_SELECT}</select><br />
		<div style="margin: 10px 10px 10px 10px; font-size: 0.95em;">
			{L_ORDER}&nbsp;<select name="sd">{S_ORDER_SELECT}</select>
			&nbsp;{L_NO_POSTS_ONLY}&nbsp;&nbsp;<input name="no_posts" type="checkbox" class="radio"<!-- IF NOPOSTS -->checked<!-- ENDIF -->/>
			&nbsp;&nbsp;<input class="button2" name="search" type="submit" value="{L_SEARCH}" />
			<p style="font-size: 1em; padding-top: 5px;">{L_F_EXPLAIN}</p>
		</div>
	</fieldset>
	<!-- IF .row  -->
	<fieldset style="margin: 0px;">
		<legend>{L_LEGEND}</legend>
		<dl>
			<dt style="width: 5%;"><ul class="stop"><li style="margin-left: 18px; color:#228822"><i class="icon find fa-fw" aria-hidden="true"></i></li></ul></dt>
			<dd style="margin: 0px 0px 0px 5%;">{L_USER_CHK}</dd>
		</dl>
		<dl>
			<dt style="width: 5%;"><ul class="stop"><li style="margin-left: 18px;"><i class="icon fa-info fa-fw" aria-hidden="true"></i></li></ul></dt>
			<dd style="margin: 0px 0px 0px 5%;">{L_EM_NOT_FIND}</dd>
		</dl>
		<dl>
			<dt style="width: 5%;"><ul class="stop"><li style="margin-left: 18px;"><i class="icon fa-exclamation fa-fw" aria-hidden="true"></i></li></ul></dt>
			<dd style="margin: 0px 0px 0px 5%;">{L_EM_IS_FIND}</dd>
		</dl>
		<dl>
			<dt style="width: 5%;"><ul class="stop"><li style="margin-left: 18px;"><i class="icon fa-check fa-fw" aria-hidden="true"></i></li></ul></dt>
			<dd style="margin: 0px 0px 0px 5%;">{L_FIND}</dd>
		</dl>
		<dl>
			<dt style="width: 5%;"><ul class="stop"><li style="margin-left: 18px; color:#BC2A4D"><i class="icon fa-exclamation-triangle fa-fw" aria-hidden="true"></i></li></ul></dt>
			<dd style="margin: 0px 0px 0px 5%;">{L_IS_FIND}</dd>
		</dl>
		<dl>
			<dt style="width: 5%;"><div style="width: 24px; height: 18px; background-color: #FCDEC0;margin-left: 16px;border-radius: 4px; border: 1px solid #C7C3BF;"></div></dt>
			<dd style="margin: 0px 0px 0px 5%;">{L_CHK_FAIL_EXPLAIN}</dd>
		</dl>
	</fieldset>
	<!-- ENDIF -->
	<fieldset>
		<legend></legend>
		<p>{L_ENTER_APY}: <input name="apy_key" type="text" value="{APY_KEY}" size="16" maxlength="16">&nbsp;
		<input class="button2" name="save" type="submit" value="{L_SAVE}" />&nbsp;
		<a href="https://www.stopforumspam.com/signup" target="_blank">{L_GET_APY_KEY}</a></p>
	</fieldset>
</form>
<!-- INCLUDE overall_footer.html -->
