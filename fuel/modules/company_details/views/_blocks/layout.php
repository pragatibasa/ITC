<div id="innerpanel">
    &nbsp;
    &nbsp;
    <fieldset>
        <legend><strong>Company Details</strong><br/></legend>
        &nbsp;<form id="cisave" method="post" name="companyDetails" action="">

            <div>
                <table cellpadding="0" cellspacing="10" border="0">
                    <tr>
                        <td>
                            <label>The name of the company<span class="required">*</span></label>
                        </td>
                        <td>
                            <input id="cname" type="text" name="cname" value=""/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>The default name or identifier to use for all receivable operations.<span
                                        class="required">*</span></label>
                        </td>
                        <td>
                            <input id="ide_receive" name="ide_receive" type="text" value=""/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>The default name or identifier to use for all payable operations.<span
                                        class="required">*</span></label>
                        </td>
                        <td>
                            <input id="ide_payable" name="ide_payable" type="text" value="<?=$data[0]->identifier_payable?>"/>

                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Head office address<span class="required">*</span></label>
                        </td>
                        <td><textarea id="addr1" name="headOffice" type="text"></textarea>&nbsp;&nbsp;<span>Info : Please enter the exact head office address to be displayed on the bills</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Branch office address<span class="required">*</span></label>
                        </td>
                        <td><textarea id="addr2" name="branchOffice" type="text"></textarea>&nbsp;&nbsp;<span>Info : Please enter the exact branch office address to be displayed on the bills</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Enter the general company contact number</label>
                        </td>
                        <td>
                            <input id="contact_no" name="contact_number" type="text" value=""/>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <label>PAN NO:</label>
                        </td>
                        <td>
                            <input id="pan_no" name="pan_number" type="text" value=""/>
                        </td>
                    </tr>


                    <tr>
                        <td>
                            <label>Bank Name:</label>
                        </td>
                        <td>
                            <input id="bank_name" name="bankname" type="text" value=""/>
                        </td>
                    </tr>


                    <tr>
                        <td>
                            <label>Branch Name:</label>
                        </td>
                        <td>
                            <input id="branch_name" name="branchname" type="text" value=""/>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <label>IFSC Code:</label>
                        </td>
                        <td>
                            <input id="ifsc_code" name="ifsccode" type="text" value=""/>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <label>A/C No:</label>
                        </td>
                        <td>
                            <input id="account_no" name="accountno" type="text" value=""/>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <label>Enter the general company email address</label>
                        </td>
                        <td>
                            <input id="email" name="email" type="text" value=""/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Enter GST registration number</label>
                        </td>
                        <td>
                            <input id="duty_no" name="gstNumber" type="text" value=""/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Enter TIN number</label>
                        </td>
                        <td>
                            <input id="tin_no" name="tinNumber" type="text" value=""/>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="pad-10">
                <input id="newsize" class="btn btn-success" type="button" value="Save" onClick="functionsave(); "/>
                &nbsp; &nbsp; &nbsp;
            </div>
        </form>
    </fieldset>

</div>

<script language="javascript" type="text/javascript">

    function inwardregistrybutton(id) {
        $.ajax({
            type: "POST",
            success: function () {
                setTimeout("location.href='<?= site_url('fuel/company_details_entry'); ?>'", 100);
            }
        });
    }

    function functionsave() {
        var data = $('form').serialize();
        $.ajax({
            type: "POST",
            url: "<?php echo fuel_url('company_details/savedetails');?>/",
            data: data,
            success: function (msg) {
                alert("Company details saved successfully");
            }
        });

    }
</script>

