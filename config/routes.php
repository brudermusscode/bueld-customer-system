<?php

use Bruder\Application\Router;

/**
 * @var Router $Router
 */

$Router->get("/404", "error/404", title: "Bruder, was geht jetzt?");
$Router->get("/", "home/index", title: APP_NAME);
// $Router->get("/doc/:document", "document/index", title: "View documents");
$Router->get("/action/new", "action/new", return: "JSON");

/**
 * @routes /authentication
 */
$Router->get("/authentication/login", "authentication/login", return: "JSON");
$Router->post("/authentication/create", "authentication/create", return: "JSON");

/**
 * @route /leasing
 */
$Router->get("/leasing/new", "leasing/new", return: "JSON");
$Router->post("/leasing/create", "leasing/create", return: "JSON");

/**
 * @route /employees
 */
$Router->get("/employees", "employee/index", title: "Mitarbeiter");


/**
 * @routes /document
 */
$Router->post("/document/get", "document/get", return: "JSON");

/**
 * ? @route /master
 */
$Router->get("/master", "master/index", title: "Stammdaten - Kategorien");

/**
 * ? @route /master/repair
 */
$Router->get("/master/repair/types", "repair/type/index", title: "Stammdaten - Reparaturen");
$Router->get(
  "/master/repair/types/:ppage",
  "repair/type/index",
  constraints: [
    "ppage" => "\d+"
  ],
  title: "Stammdaten - Reparaturen"
);
$Router->get(
  "/master/repair/types/:ppage/:sort",
  "repair/type/index",
  constraints: [
    "ppage" => "\d+"
  ],
  title: "Stammdaten - Reparaturen"
);
$Router->get(
  "/master/repair/types/:ppage/:sort/:filter",
  "repair/type/index",
  constraints: [
    "ppage" => "\d+"
  ],
  title: "Stammdaten - Reparaturen"
);

/**
 * ? @route /master/customers
 */
$Router->get("/master/customers", "customer/index", title: "Kunden");
$Router->get(
  "/master/customers/:ppage",
  "customer/index",
  constraints: [
    "ppage" => "\d+"
  ],
  title: "Stammdaten - Kunden"
);
$Router->get(
  "/master/customers/:ppage/:sort",
  "customer/index",
  constraints: [
    "ppage" => "\d+"
  ],
  title: "Stammdaten - Kunden"
);
$Router->get(
  "/master/customers/:ppage/:sort/:filter",
  "customer/index",
  constraints: [
    "ppage" => "\d+"
  ],
  title: "Stammdaten - Kunden"
);

// TODO: Add proper titles in router

/**
 * ? @route /customer
 */
$Router->get("/customer/:id", "customer/show", title: "Kunde ansehen");
$Router->get("/customer/:id/:customer_object_type", "customer/show", title: "Kunde ansehen");
$Router->get("/customer/object/new", "customer/object/new", return: "JSON");
$Router->post("/customer/object/create", "customer/object/create", return: "JSON");

/**
 * ? @route /orders
 */
$Router->get("/orders", "order/index", title: "Alle Aufträge");
$Router->get(
  "/orders/:ppage",
  "order/index",
  constraints: [
    "ppage" => "\d+"
  ],
  title: "Aufträge"
);
$Router->get(
  "/orders/:ppage/:sort",
  "order/index",
  constraints: [
    "ppage" => "\d+"
  ],
  title: "Aufträge"
);
$Router->get(
  "/orders/:ppage/:sort/:filter",
  "order/index",
  constraints: [
    "ppage" => "\d+"
  ],
  title: "Aufträge"
);
$Router->get(
  "/orders/:ppage",
  "order/index",
  constraints: [
    "ppage" => "\d+"
  ],
  title: "Aufträge"
);
$Router->get(
  "/orders/:ppage/:sort",
  "order/index",
  constraints: [
    "ppage" => "\d+"
  ],
  title: "Aufträge"
);
$Router->get(
  "/orders/:ppage/:sort/:filter",
  "order/index",
  constraints: [
    "ppage" => "\d+"
  ],
  title: "Aufträge"
);

/**
 * ? @route /customer
 */
$Router->get("/customer/search", "customer/search", return: "JSON");
$Router->get("/customer/find", "customer/find", return: "JSON");

/**
 * ? @route /bookmark
 */
$Router->post("/bookmark/create", "bookmark/create", return: "JSON");

/**
 * ? @route /repair/order
 */
$Router->get("/repair/order/:id", "repair/order/show", title: "Reparatur-Auftrag");
$Router->get("/repair/order/new", "repair/order/new", title: "Reparatur-Auftrag erstellen");
$Router->get(
  "/repair/order/edit/:id",
  "repair/order/edit",
  constraints: [
    "id" => "\d+"
  ],
  title: "Reparatur-Auftrag bearbeiten"
);
$Router->get(
  "/repair/order/edit/:id/:sub",
  "repair/order/edit",
  constraints: [
    "id" => "\d+"
  ],
  title: "Reparatur-Auftrag bearbeiten"
);
$Router->post("/repair/order/finish", "repair/order/finish", return: "JSON");
$Router->post("/repair/order/update", "repair/order/update", return: "JSON");
$Router->post("/repair/order/create", "repair/order/create", return: "JSON");
$Router->post("/repair/order/delete", "repair/order/delete", return: "JSON");

/**
 * ? @route /repair/order/part
 */
$Router->get("/repair/order/part/new", "repair/order/part/new", title: "Ersatzteil hinzufügen");
$Router->post("/repair/order/part/create", "repair/order/part/create", return: "JSON");
$Router->post("/repair/order/part/delete", "repair/order/part/delete", return: "JSON");

/**
 * ? @route /repair/type
 */
$Router->get("/repair/type/search", "repair/type/search", return: "JSON");
$Router->get("/repair/type/edit", "repair/type/edit", return: "JSON");
$Router->post("/repair/type/update", "repair/type/update", return: "JSON");
$Router->post("/repair/type/delete", "repair/type/delete", return: "JSON");

/**
 * ? @route /brand
 */
$Router->get("/brand/search", "brand/search", return: "JSON");
