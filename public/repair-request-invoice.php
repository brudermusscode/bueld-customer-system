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
  <div style="padding-top: 234px;"></div>

  <div style="padding: 72px;padding-top:0;">
    <p bold style="font-size:28px;margin-bottom:12px;">Rechnung <span style="color: #AD1457;">{ORDER_REPAIR_TYPE}</span></p>



    <!--- HEADER INFORMATION --->
    <div>

      {CUSTOMER}

      {LEASING}

      <div rt style="
          background: rgba(0, 0, 0, 0.08);
          padding: 18px;
          width: 212px;
          border-radius: 18px;
        ">
        <p bold style="padding-bottom: 8px">
          Bedient von {ACTIVE_EMPLOYEE_NAME}
        </p>

        <div>
          <div lt style="width: calc(100% - 132px)">
            <p>Datum:</p>
            <p>Referenznummer:</p>
            <p>Kundennummer:</p>
          </div>

          <div rt style="width: 88px">
            <p>{ORDER_INVOICE_DATE}</p>
            <p>{ORDER_REFERENCE_ID}</p>
            <p>{CUSTOMER_ID}</p>
          </div>

          <div cl></div>
        </div>
      </div>

      <div cl></div>
    </div>



    <!--- CUSTOMER ADDONS & BRAND --->
    <div style="width:100%;height:28px;margin-top:18px;margin-bottom:32px;">

      {ORDER_CUSTOMER_ADDONS}

      {ORDER_OBJECT_BRAND}

      <div cl></div>
    </div>



    <!--- REPAIRS --->
    <div style="margin-bottom: 6px;border-bottom: 2px solid rgba(0, 0, 0, 0.04);padding-block: 0.2em;margin-top: 24px;">
      <p bold inline style="width: calc(100% - 13% - 21%)">
        Vorgenommene Reparaturen
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

    <div>&nbsp;</div>

    <div style="padding-block: .4em;">
      <p
        bold
        inline
        style="width: calc(100% - 148px); text-align: right; font-size: 16px">
        Gesamtkosten
      </p>
      <p bold inline style="width: 144px; text-align: right; font-size: 16px">
        {FULL_PRICE}
      </p>
    </div>

    <div style="opacity:.6;padding-block: .4em;">
      <p
        inline
        style="width: calc(100% - 148px); text-align: right; font-size: 16px">
        Enthaltene MwSt.
      </p>
      <p inline style="width: 144px; text-align: right; font-size: 16px">
        {FULL_PRICE_UST}
      </p>
    </div>

    <div style="margin-top: 42px">
      <p bold style="font-size: 16px; margin-bottom: 8px">
        Gewährleistung
      </p>

      {ORDER_TERMS}

      <!-- <p>
        Auf Reparturen aller Art gewähren wir Ihnen 12 Monate Gewährleistung. Für Verschleißschäden, die sich aus der Nutzung, des Produktes ergeben besteht keine Gewährleistung, insbesondere wenn diese als gebrauchsüblich anzusehen sind.
      </p> --->
    </div>
  </div>
</body>

</html>