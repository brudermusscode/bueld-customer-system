<!doctype html>
<html>

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <style>
    * {
      font-family: "Roboto", sans-serif !important;
    }

    [fl] {
      display: flex;
    }

    [flexone] {
      flex: 1;
    }

    [flex-wrap] {
      flex-wrap: wrap;
    }

    [jucsb] {
      justify-content: space-between;
    }

    [jucc] {
      justify-content: center;
    }

    [fldircol] {
      flex-direction: column;
    }

    [alistart] {
      align-items: flex-start;
    }

    [alic] {
      align-items: center;
    }

    [bold] {
      font-weight: bold;
    }

    * {
      margin: 0;
      padding: 0;
    }

    [lt] {
      float: left;
    }

    [rt] {
      float: right;
    }

    [cl] {
      clear: both;
    }

    [inline] {
      display: inline-block;
      vertical-align: top;
    }
  </style>
</head>

<body style="font-size: 14px; color: #333">
  <div style="margin: 16px;border-radius: 24px;line-height: 1;background: #cd0005;padding: 64px;padding-top: 72px;color: white;">
    <p style="font-size: 24px">Deine</p>
    <p lt bold style="font-size: 38px;">Rechnung</p>
    <div rt style="border-radius: 50%;background: white;box-sizing: border-box;height: 96px;width: 120px;text-align: CENTER;padding-top: 22px;margin-top: -52px;margin-bottom: -24px;">
      <img src="{LOGO_PATH}" style="height: 72px" />
    </div>
    <div cl></div>
  </div>

  <div style="padding: 72px; padding-top: 12px">
    <!--- HEADER INFORMATION --->
    <div>
      <div lt>
        <div style="margin-top: 24px">
          <p bold style="font-size: 16px">Kunde</p>
          <p>{CUSTOMER_NAME}</p>
          <p>{CUSTOMER_ADDRESS}</p>
          <p>{CUSTOMER_POSTCODE_CITY}</p>
        </div>
      </div>

      <div rt style="background: rgba(0, 0, 0, 0.08); padding: 24px; width: 36%;border-radius:18px;">
        <p bold style="padding-bottom: 8px">
          Repariert von {ACTIVE_EMPLOYEE_NAME}
        </p>

        <div>
          <div lt style="width: calc(100% - 132px)">
            <p>Auftragsdatum:</p>
            <p>Referenznummer:</p>
            <p>Kundennummer:</p>
          </div>

          <div rt style="width: 120px">
            <p>{ORDER_DATE}</p>
            <p>{ORDER_REFERENCE_ID}</p>
            <p>{CUSTOMER_ID}</p>
          </div>

          <div cl></div>
        </div>
      </div>

      <div cl></div>
    </div>

    <!--- REPAIRS --->
    <div style="margin-bottom: 6px;border-bottom: 2px solid rgba(0, 0, 0, 0.04);padding-block: 0.2em;margin-top: 24px;">
      <p bold inline style="width: calc(100% - 13% - 21%)">
        Reparaturen
      </p>
      <p bold inline style="width: 12%">Anzahl</p>
      <p bold inline style="width: 20%; text-align: right">Preis</p>
    </div>

    {ORDER_REPAIRS}

    <!-- <div style="border-bottom: 1px solid rgba(0, 0, 0, 0.04);padding-top: 4px;padding-bottom: 4px;">
      <div inline style="width: calc(100% - 13% - 21%)">{REPAIR_TYPE}</div>
      <p inline style="width: 12%">{QUANTITY}</p>
      <p inline style="width: calc(20% + 4px); text-align: right">{PRICE}</p>
    </div> -->

    <!--- PARTS --->
    <div style="margin-top: 12px;margin-bottom: 6px;border-bottom: 2px solid rgba(0, 0, 0, 0.04);padding-block: 0.2em;">
      <p bold inline style="width: calc(100% - 13% - 21%)">
        Benötigte Ersatzteile
      </p>
      <p bold inline style="width: 12%"></p>
      <p bold inline style="width: 20%; text-align: right"></p>
    </div>

    {ORDER_PARTS}

    <!-- <div style="border-bottom: 1px solid rgba(0, 0, 0, 0.04);padding-top: 4px;padding-bottom: 4px;">
      <div inline style="width: calc(100% - 13% - 21%)">
        {REPLACEMENT_PART}
      </div>
      <p inline style="width: 12%">{QUANTITY}</p>
      <p inline style="width: calc(20% + 4px); text-align: right">{PRICE}</p>
    </div> -->

    <div style="padding-block: 0.4em; margin-top: 12px">
      <p
        bold
        inline
        style="width: calc(100% - 148px); text-align: right; font-size: 16px">
        Gesamtpreis
      </p>
      <p bold inline style="width: 144px; text-align: right; font-size: 16px">
        {FULL_PRICE}
      </p>
    </div>

    <div style="margin-top: 42px">
      <p bold style="font-size: 16px; margin-bottom: 8px">
        Informationen
      </p>

      {ORDER_TERMS}

      <!-- <p style="margin-top: 12px; font-size: 12px">
          Auf Reparturen aller Art gewähren wir Ihnen 12 Monate Gewährleistung. Für Verschleißschäden, die sich aus der Nutzung,
          des Produktes ergeben besteht keine Gewährleistung, insbesondere wenn diese als gebrauchsüblich anzusehen sind.
      </p> -->
    </div>
  </div>
</body>

</html>