<h1>Set VPN</h1>
<div style="margin: 30px 10px;" class="breadcrumb">
    {$breadcrumbnav|replace:' >':' / '} / {$menu}
</div>
{if $ruid eq 'error'}
    <div class="alert alert-danger" style="margin-bottom: 50px;"><strong>Error!</strong> Please ask support to configure VPN for you.</div>
{else}
    <div class="row-fluid" style="margin-bottom: 50px;">
        <div class="col-md-6 span6">
            <form method="post">
                {if $password neq ''}
                    {if $error eq 'password'}
                        <div class="alert alert-danger">
                            <strong>Password incorrect!</strong> password must be at least 6 characters long.
                        </div>
                        <h4>
                            Username: <b><span>S{$rid}-{$ruid}</span></b>
                        </h4><br>
                        <h4>Password:
                            <input class="form-control" type="password" placeholder="Password" name="password" value="">
                        </h4><br>
                        <input type="hidden" name="userid" value="{$hashruid}">
                        <input type="submit" value="Set VPN" class="btn btn-block btn-success btn-lg pull-right" >
                    {else}
                        {if $error eq 'failed'}
                            <div class="alert alert-danger">
                                <strong>Error!</strong>{$msg}
                            </div>
                        {else}
                            <div class="alert alert-success">
                                <strong>Done!</strong> VPN password changed.
                            </div>
                            <h4>
                                Username: <b><span>S{$rid}-{$ruid}</span></b>
                            </h4><br>
                        {/if}
                    {/if}
                {else}
                    <h4>
                        Username: <b><span>S{$rid}-{$ruid}</span></b>
                    </h4><br>
                    <h4>Password:
                        <input class="form-control" type="password" placeholder="Password" name="password" value="">
                    </h4><br>
                    <input type="hidden" name="userid" value="{$hashruid}" >
                    <input type="submit" value="Set VPN" class="btn btn-block btn-success btn-lg pull-right" >
                {/if}
        </form>
    </div>
    <div class="col-md-6 span6" style="margin-bottom: 50px;padding-left: 40px;">
        <h3 style="margin-bottom: 20px;">VPN Type: <b style="color: orangered">Open VPN</b></h3>
        {if $content eq ''}
            Please contact Support for VPN Connection.
        {/if}
        {$content}
    </div>
    </div>
{/if}