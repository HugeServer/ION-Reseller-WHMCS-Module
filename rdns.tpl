<h1>rDNS</h1>
<div style="margin: 30px 10px;" class="breadcrumb">
    {$breadcrumbnav|replace:' >':' / '} / {$menu}
</div>

{if $post eq 'yes'}
    {if $error eq 'failed'}
        <div class="alert alert-danger">
            <strong>Error!</strong> {$message}
        </div>
    {else}
        <div class="alert alert-success">
            <strong>Done!</strong> rDNS records successfully updated.
        </div>
    {/if}
{/if}

{if $ip neq ''}
    {if $error neq 'access'}
        <h4>IP: {$ip}</h4><br>
        <form method="post">
            <input class="btn btn-success" type="submit" value="Update" style="margin-bottom: 30px;">
            <table class="table table-bordered" width="100%">
                <tr>
                    <th width="33%">IP Address</th>
                    <th width="33%">TTL</th>
                    <th>Record</th>
                </tr>
                {if $fetch eq 'no'}
                    <tr>
                        <td style="vert-align: middle;">{$ip}<input type="hidden" value="' . $_GET['ip'] .'" name="ip[]"></td>
                        <td><input class="form-control" style="width:90%;" type="text" value="14400" name="ttl[]"></td>
                        <td><input type="text" value="" name="content[]" style="width:90%;" class="form-control"></td>
                    </tr>
                {else}
                    {$record}
                {/if}
            </table>
            <input type="submit" class="btn btn-success" value="Update"  style="margin-top: 30px;margin-bottom: 50px;">
        </form>
    {else}
         <div class="alert alert-danger" style="margin-bottom: 50px;">
            <strong>Forbidden!</strong> Access denied.
         </div>
    {/if}
{else}
    <h3>rDNS Zones</h3><br>
    <table width="100%" class="table table-bordered" style="margin-bottom: 50px;">
        <tr>
            <th width="33%">CIDR</th>
            <th width="33%">First IP</th>
            <th>Last IP</th>
        </tr>
        <tr>
            {$content}
        </tr>
    </table>
{/if}