class ImageSourceProvider {
  constructor(endpoint, offsetKey, limitKey) {
    this.endpoint = endpoint;
    this.options = { offsetKey: offsetKey, limitKey: limitKey };
  }

  getEndpoint(offset, limit) {
    const parameters = [];
    if (typeof offset !== "undefined" && offset) {
      parameters.push(this.options.offsetKey + "=" + offset);
    }
    if (typeof limit !== "undefined" && limit) {
      parameters.push(this.options.limitKey + "=" + limit);
    }
    return (
      this.endpoint + (parameters.length > 0 ? "?" + parameters.join("&") : "")
    );
  }

  query() {}
}

class ImageSourceItem {
  constructor(thumbUrl, title) {
    this.thumbUrl = thumbUrl;
    this.title = title;
  }
  getTitle() {
    return this.title;
  }
  getThumbUrl() {
    return this.thumbUrl;
  }
}

class ImageSourceHydrator {
  constructor() {}
  hydrate(dataItem) {
    return new ImageSourceItem(dataItem.thumbnailUrl, dataItem.title);
  }
}

class TypicodePhotosProvider extends ImageSourceProvider {
  constructor() {
    super("https://jsonplaceholder.typicode.com/photos", "_start", "_limit");
  }
}

class Pager {
  constructor(perPage, initialPage) {
    this.perPage = perPage;
    this.page = initialPage;
  }

  getPage() {
    return this.page;
  }

  setPage(page) {
    this.page = page;
  }

  getPerPage() {
    return this.perPage;
  }
}

class ImagesDataSource {
  constructor(dataSourceProvider) {
    this.provider = dataSourceProvider;
  }

  query(offset, limit) {
    return fetch(this.provider.getEndpoint(offset, limit), {})
      .then(response => {
        if (response.ok) {
          return Promise.resolve(response);
        } else {
          return Promise.reject(new Error(this.endpoint + " loading failed"));
        }
      })
      .catch(error => {
        console.log(error);
      });
  }
}

class ViewElement {
  constructor(parentViewElement, name, attributes, content) {
    this.parentViewElement = parentViewElement;
    this.element = document.createElement(name);
    for (let k in attributes) {
      this.element.setAttribute(k, attributes[k]);
    }
    if (content) {
      this.element.innerHTML = content;
    }
    this.inDom = false;
    if (parentViewElement instanceof ViewElement) {
      parentViewElement.append(this);
    }
    this.childs = [];
  }

  getElement() {
    return this.element;
  }

  createChild(name, attributes, content) {
    return new ViewElement(this, name, attributes, content);
  }

  append(viewElement) {
    this.childs.push(viewElement);
  }

  render() {
    if (!this.inDom) {
      if (this.parentViewElement instanceof HTMLElement) {
        this.parentViewElement.appendChild(this.element);
      } else {
        this.parentViewElement.getElement().appendChild(this.element);
      }
      this.inDom = true;
    }
    this.childs.forEach(childViewElement => {
      childViewElement.render();
    });
  }

  hide() {
    this.element.hidden = true;
  }

  remove() {
    this.element.remove();
  }
}

class ImageViewElement extends ViewElement {
  constructor(parentViewElement, source) {
    super(
      parentViewElement,
      "div",
      {
        class: "image-container"
      },
      null
    );
    this.createChild("div", {
      class: "image",
      style: "background-image:url(" + source + ");"
    }).render();
  }
}

class ImagesPanelView extends ViewElement {
  constructor(parentDomElement, dataSource, imageHydrator, pager) {
    super(parentDomElement, "div", {
      class: "images-panel"
    });

    this.dataSource = dataSource;
    this.imageHydrator = imageHydrator;

    this.pager = pager;

    this.texts = {
      buttonLoadMoreLabel: "Загрузить еще",
      buttonLoadingLabel: "Пожалуйста подождите ...",
      frameHeadingLabel: "Страница"
    };

    this.buttons = { next: null };

    this.content = this.createChild("div", { class: "content" });
    this.navigator = this.createChild("div", { class: "navigator" });

    this.loading = false;

    this.initContent();
    this.initNavigator();
  }

  initNavigator() {
    this.navigator.render();
    this.buttons.next = this.navigator.createChild(
      "button",
      { class: "button-next" },
      this.texts.buttonLoadMoreLabel
    );
    this.buttons.next.getElement().addEventListener("click", () => {
      this.pager.setPage(this.pager.getPage() + 1);
      this.load();
    });
    this.buttons.next.render();
  }

  initContent() {
    this.content.render();
  }

  onStartLoading() {
    this.loading = true;
    const button = this.buttons.next.getElement();
    button.innerHTML = this.texts.buttonLoadingLabel;
    button.setAttribute("disabled", true);
  }
  onStopLoading() {
    this.loading = false;
    const button = this.buttons.next.getElement();
    button.innerHTML = this.texts.buttonLoadMoreLabel;
    button.removeAttribute("disabled");
  }

  getOffset() {
    return (this.pager.getPage() - 1) * this.pager.getPerPage();
  }

  createNextContentFrame() {
    const frame = this.content.createChild("div", {
      class: "content-frame"
    });
    frame.createChild(
      "h4",
      {},
      this.texts.frameHeadingLabel + " " + this.pager.getPage()
    );
    return frame;
  }

  addImagesToView(data, view) {
    data.forEach(item => {
      const image = this.imageHydrator.hydrate(item);
      new ImageViewElement(view, image.getThumbUrl());
    });
  }

  load() {
    this.onStartLoading();
    const offset = this.getOffset();
    this.dataSource
      .query(offset, this.pager.getPerPage())
      .then(response => {
        const total = parseInt(response.headers.get("x-total-count"));
        const totalRequested = this.getOffset() + this.pager.getPerPage();
        if (totalRequested >= total) {
          this.buttons.next.hide();
        }
        return response.json();
      })
      .then(data => {
        if (data.length < this.pager.getPerPage()) {
          this.buttons.next.hide();
        }
        const nextFrame = this.createNextContentFrame();
        this.addImagesToView(data, nextFrame);

        this.content.render();
        this.onStopLoading();
      })
      .catch(error => {
        this.onStopLoading();
        console.log(error);
      });
  }
}

const pager = new Pager(9, 1);
const dataSource = new ImagesDataSource(new TypicodePhotosProvider());
const hydrator = new ImageSourceHydrator();
const appDiv = document.getElementById("app");

const view = new ImagesPanelView(appDiv, dataSource, hydrator, pager);
// Render view
view.render();

// Load first page
view.load();
