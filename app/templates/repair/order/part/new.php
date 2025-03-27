<?php

include dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Http\Request;

/**
 * @var Request $Request
 */

/**
 * @var int
 */
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

/**
 * @var string
 */
$type = filter_input(INPUT_GET, "type", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * Any param is not set?
 */
if (!$id || !$type)
  exit($Request->error());

/**
 * Begin output buffer.
 */
ob_start();

?>

<prompt fl alic jucc>
  <div close-overlay></div>

  <div content-width=smoler center-content>
    <form data-form="repair:order:part:create">
      <box-model filled=lighter rounded elevated=mid fl fldircol gap=mid p42 animation=open>
        <div fl alic fldircol gap=smol+>
          <mi midplus slight>build</mi>
          <p text midler semibold>Ersatzteil hinzufügen</p>
        </div>

        <div fl fldircol gap>
          <div fl fldircol gap=smol>
            <p text bold smolplus>Name</p>
            <div input material flexone>
              <input
                name=name
                tabindex=100
                type="text"
                autofocus
                required
                enter-submitable
                placeholder="Bosch Intuvia Display" />
            </div>
          </div>

          <!-- <div fl fldircol gap=smol>
            <div fl fldircol>
              <p text bold smolplus>Artikel-Nummer</p>
              <p text smol slight>Kann frei gelassen werden</p>
            </div>
            <div input material flexone>
              <input
                name=article_id
                tabindex=101
                type="text"
                enter-submitable
                placeholder="123456789" />
            </div>
          </div> -->

          <div fl alic jucsb gap>
            <div fl fldircol>
              <p text smolplus bold>EAN</p>
              <p text smol slight>Kann frei gelassen werden</p>
            </div>
            <div input material has-icon=right style=width:11.4em;>
              <mi color=green bold>add_circle</mi>
              <input
                name=article_id
                tabindex=102
                type="text"
                enter-submitable
                placeholder="123456789" />
            </div>
          </div>

          <div fl alic jucsb gap>
            <div fl fldircol>
              <p text smolplus bold>Preis</p>
              <p text smol slight>Kann frei gelassen werden</p>
            </div>
            <div input material has-icon=right style=width:11.4em;>
              <mi color=green bold>euro</mi>
              <input
                name=price
                tabindex=102
                type="text"
                enter-submitable
                placeholder="0,00" />
            </div>
          </div>

          <div fl alic jucsb gap>
            <div fl fldircol>
              <p text smolplus bold>Menge</p>
              <p text smol slight>Kann frei gelassen werden</p>
            </div>
            <div input material has-icon=right style=width:11.4em;>
              <mi color=green bold>add_circle</mi>
              <input
                name=amount
                tabindex=102
                type="number"
                enter-submitable
                placeholder="1"
                value=1 />
            </div>
          </div>
        </div>

        <input type=hidden name=id value="<?= $id ?>" />
        <input type=hidden name=type value="<?= $type ?>" />

        <div fl jucsb>
          <mbutton o-closer tabindex=104 size=mid material clean hoverable>
            <p text smolplus>Abbrechen</p>
          </mbutton>
          <mbutton tabindex=103 submit-closest size=mid material background=company color=company3>
            <p text semibold>Hinzufügen</p>
          </mbutton>
        </div>
      </box-model>
    </form>
  </div>
</prompt>

<?php

exit($Request->success(data: ob_get_clean()));
