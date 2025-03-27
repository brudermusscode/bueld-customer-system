export const routes = {
  not_found: {},

  "": {
    mark: "home",
  },

  home: {
    mark: "home",
  },

  repair: {
    mark: "repair",
  },

  orders: {
    mark: "orders",
  },

  master: {
    mark: "master",
  },

  customers: {
    mark: "customers",
  },

  employees: {
    mark: "employees",
  },
};

export const router = async (route) => {
  let match = route in routes ? routes[route] : routes.not_found;
  let key = route in routes ? route : "not_found";

  match["page"] = key;

  return match;
};
