<?php

include dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Http\Request;

/**
 * @var Request $Request
 */

/**
 * Begin output buffer.
 */
ob_start();

?>

<prompt fl alic jucc>
  <div close-overlay></div>

  <div content-width=smolest center-content>
    <form request="authentication:create" full-reload responder>
      <box-model background=nav rounded=wide elevated=mid fl fldircol gap=mid p62 animation=open>
        <div fl alic fldircol gap=smol+>
          <div style="height:82px;width:82px;" background=company color=company3 fl alic jucc circled>
            <mi midplus slight>passkey</mi>
          </div>
          <p text midler semibold>Master-Login</p>
        </div>

        <div fl fldircol gap=smol>
          <div input material flexone>
            <input
              name=username
              tabindex=100
              type="text"
              autofocus
              required
              enter-submitable
              placeholder="Username" />
          </div>
          <div input material flexone>
            <input
              name=password
              tabindex=101
              type="password"
              autofocus
              required
              enter-submitable
              placeholder="Passwort" />
          </div>
        </div>

        <div fl jucsb>
          <mbutton o-closer tabindex=102 size=mid material clean hoverable>
            <p text smolplus>Abbrechen</p>
          </mbutton>
          <mbutton tabindex=103 submit-closest size=mid material background=company color=company3>
            <p text semibold>Anmelden</p>
          </mbutton>
        </div>
      </box-model>
    </form>
  </div>
</prompt>

<?php

exit($Request->success(data: ob_get_clean()));
