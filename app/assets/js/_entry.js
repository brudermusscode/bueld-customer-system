import * as App from "./application";
import * as Router from "./router";
import * as Cookie from "./cookies";
import * as Frontend from "./frontend";
import * as Image from "./images";
import * as Page from "./page";
import * as Utils from "./utils";
import * as Window from "./window";
import * as Request from "./requests";

import * as SelectModel from "./elements/mselect";
import * as SelectModel from "./elements/mradio";
import * as InputModel from "./elements/minput";
import * as Responder from "./elements/responder";
import Overlay from "./elements/overlay";
import * as mselect from "./elements/mselect";

import * as Customer from "./pages/customer";
import * as RepairOrder from "./pages/repair-order";
import * as RepairOrder from "./pages/document";

import * as Global from "./pages/global";

/**
 * Makes Frontend available in the DOM.
 * window.Frontend = Frontend;
 */

/**
 * SCSS
 */
import "../scss/application.scss";
