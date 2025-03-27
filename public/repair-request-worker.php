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
    <p bold style="font-size:28px;margin-bottom:12px;">Reparatur-Auftrag <span style="color: #AD1457;">{ORDER_REPAIR_TYPE}</span></p>



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
            <p>Auftragsdatum:</p>
            <p>Referenznummer:</p>
            <p>Kundennummer:</p>
          </div>

          <div rt style="width: 88px">
            <p>{ORDER_DATE}</p>
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
    <div
      style="
          margin-bottom: 6px;
          border-bottom: 2px solid rgba(0, 0, 0, 0.04);
          padding-top: 0.2em;
          padding-bottom: 0.2em;
        ">
      <p bold inline style="width: calc(100% - 13% - 21%)">
        Voraussichtliche Reparaturen
      </p>
      <p bold inline style="width: 12%">Anzahl</p>
      <p bold inline style="width: 20%; text-align: right">Preis</p>
    </div>

    {ORDER_REPAIRS}



    <!--- PARTS --->
    <div
      style="
          margin-top: 12px;
          margin-bottom: 6px;
          border-bottom: 2px solid rgba(0, 0, 0, 0.04);
          padding-top: 0.2em;
          padding-bottom: 0.2em;
        ">
      <p bold inline style="width: calc(100% - 13% - 21%)">
        Benötigte Ersatzteile
      </p>
      <p bold inline style="width: 12%"></p>
      <p bold inline style="width: 20%; text-align: right"></p>
    </div>

    {ORDER_PARTS}

    <div style="padding-top: 0.4em; padding-bottom: 0.4em; margin-top: 12px">
      <p
        bold
        inline
        style="width: calc(100% - 148px); text-align: right; font-size: 16px">
        Voraussichtliche Kosten
      </p>
      <p bold inline style="width: 144px; text-align: right; font-size: 16px">
        {FULL_PRICE}
      </p>
    </div>

    <div style="margin-top: 42px">
      <p bold style="font-size: 14px; margin-bottom: 2px">
        Reparaturbedingungen
      </p>

      {ORDER_TERMS}
    </div>
  </div>



  <!--- NEW PAGE --->
  <div style="page-break-before: always"></div>
  <!--- NEW PAGE --->



  <div style="line-height: 1; padding: 72px; padding-top:0;padding-bottom:0;">
    <div style="padding-top: 234px;"></div>

    <div style="margin-bottom:12px;">
      <p text bold style="font-size: 32px;">
        Reparatur-Auftrag <span style="color: #AD1457;">{ORDER_REPAIR_TYPE}</span>
      </p>
    </div>

    <div>
      <div lt style="line-height: 1.2; margin-right: 24px">
        <p text style="opacity: 0.8">Objekt</p>
        <p text bold style="font-size: 14px">{ORDER_TYPE_BRAND}</p>
      </div>
      <div lt style="line-height: 1.2; margin-right: 24px">
        <p text style="opacity: 0.8">Gerätenr.</p>
        <p text bold style="font-size: 14px">{ORDER_OBJECT_ID}</p>
      </div>
      <div lt style="line-height: 1.2; margin-right: 24px">
        <p text style="opacity: 0.8">Referenznr.</p>
        <p text bold style="font-size: 14px">{ORDER_REFERENCE_ID}</p>
      </div>
      <div lt style="line-height: 1.2; margin-right: 24px">
        <p text style="opacity: 0.8">Kunde</p>
        <p text bold style="font-size: 14px">{CUSTOMER_FULL_NAME}</p>
      </div>
      <div lt style="line-height: 1.2; margin-right: 24px">
        <p text style="opacity: 0.8">Datum</p>
        <p text bold style="font-size: 14px">{ORDER_DATE}</p>
      </div>

      <div cl></div>

      <div style="line-height: 1.2; margin-right: 24px;margin-top:8px;">
        <p lt text style="font-size: 14px;background: #AD1457;color: white;padding-top:4px;padding-left:8px;padding-bottom:4px;padding-right:8px;border-radius:8px;">
          Kontaktiere den Kunden: <strong>{CUSTOMER_CONTACT_OPTION}</strong>
        </p>

        <div cl></div>
      </div>
    </div>
  </div>

  <div
    style="
        background: rgba(0, 0, 0, 0.12);
        height: 1px;
        margin-top: 24px;
        margin-bottom: 24px;
      "></div>

  <div fl fldircol style="padding: 72px; padding-top: 0">
    {ORDER_CUSTOMER_ADDONS}

    <div cl></div>

    <div style="width: 100%">
      <p text bold style="font-size: 20px">Reparaturen</p>
      {ORDER_REPAIRS_WORKER}
    </div>

    <div style="width: 100%; margin-top: 32px">
      <p text bold style="font-size: 20px">Ersatzteile</p>
      {ORDER_PARTS_WORKER}
    </div>

    <div style="margin-top: 24px">
      <p lt text bold style="padding-top: 28px">
        Datum: _________________________
      </p>

      <div rt style="width: 120px; padding-top: 16.8px">
        <div
          lt
          style="
              border-radius: 50%;
              border: 4px solid green;
              height: 32px;
              min-width: 32px;
              width: 32px;
            "></div>
        <p rt text style="font-size: 28px;padding-top:2px;">Fertig</p>
        <div cl></div>
      </div>

      <div cl></div>
    </div>
  </div>
</body>

</html>