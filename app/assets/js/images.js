export const load_images = async (arr) => {
  arr.forEach(async (img) => {
    if (img.hasAttribute("loaded")) {
      // console.log("%cAlready loaded: " + img.src, "color:grey;");
      return;
    }

    let new_img = new Image();
    new_img.src = img.src;

    if (new_img.complete) {
      img.setAttribute("loaded", true);
      // console.log("%c✅ " + img.src, "color:green;");
      return;
    }

    img.onerror = () => {
      img.onerror = "";
      // console.log("%c❌ " + img.src, "color:red;");
      return;
    };

    img.onload = () => {
      img.setAttribute("loaded", true);
      // console.log("%c✅ " + img.src, "color:green;");
      return;
    };
  });
};
