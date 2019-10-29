class Product extends HTMLElement {

    static get observedAttributes() {
        return ['product'];
    }

    constructor() {
        // Always call super first in constructor
        super();

        // Create a shadow root
        const shadow = this.attachShadow({ mode: 'open' });

        // Create parent element
        const product = document.createElement('div');
        product.setAttribute('class', 'product');

        // Create some CSS to apply to the shadow dom
        const style = document.createElement('style');

        style.textContent = `
            .center-align {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);  
            }

            .product {
                position: relative;
                overflow: hidden;
            }

            .product a {
                width: 100%;
                height: 100%;
                position: absolute;
                z-index: 10;
            }

            .product .out-of-stock {
                display: inline-block;
                width: 100%;
                height: 40px;
                position: absolute;
                top: 50%;
                z-index: 10;
                background: rgba(0,0,0,.8);
                line-height: 40px;
                text-align: center;
                font-family: var(--prod-font-family);
                font-size: 16px;
                font-weight: bold;
                color: #fff;
            }
            
            .product .img-cont {
                position: relative;
                width: 100%;
                overflow: hidden;
            }

            .product .img-canvas {
                height: 100%;
                width: 100%;
                background-clip: content-box;
                background-position: center;
                background-repeat: no-repeat;
                background-size: contain;
            }

            .product .description {
                min-height: 54px;
                display: flex;
                flex-direction: column;
                align-items: center;
                padding-top: 10px;
                border-top: 1px solid black;
            }
            
            .product .description p {
                margin: 3px 0;
                min-height: 2em;
                color: black;
                font-family: var(--prod-font-family);
                text-align: center;
            }
            
            .product .description .price .line-through {
                text-decoration: line-through;
                color: #bababa !important;
            }
            
            .product .discount {
                display: flex;
                align-items: center;
                justify-content: center;
                position: absolute;
                top: 5%;
                left: 5%;
                width: 50px;
                height: 50px;
                border-radius: 50%;
                background: rgba(0, 0, 0, 0.7);
            }
            
            .product .discount span {
                color: white;
                font-size: 16px;
                font-weight: 600;
                white-space: nowrap;
            }

            .product .layer {
                position: absolute;
                top: 0;
                left: 0;
                width: calc(100% - 2px);
                height: calc(100% - 2px);
                transition: all .15s ease; 
            }
            
            .product:hover .layer {
                border: 1px solid black;
            }

            @media screen and (min-width: 20em) {
                .product {
                    width: 140px;
                    padding: 7px;
                }

                .product .img-cont {
                    height: 200px;
                }
            }

            @media screen and (min-width: 35.5em) {
                .product {
                    width: 150px;
                    padding: 15px;
                }
            }

            @media screen and (min-width: 64em) {
                .product {
                    width: 200px;
                }
            }

            @media screen and (min-width: 80em) {
                .product .img-cont {
                    height: 250px;
                }
            
                .product .discount {
                    opacity: 1;
                    transition: all .15s ease;
                }
            
                .product .description {
                    width: 100%;
                }
            
                .product .product:hover {
                    cursor: pointer;
                }
            
                .product:hover .discount {
                    opacity: 0;
                }
            }
        `;

        // Attach the created elements to the shadow dom
        shadow.appendChild(style);
        shadow.appendChild(product);
    }

    /* 
    * è necessario popolare l'elemento "product" dopo che il componente, ovvero "<my-product></my-product>",
    * è stato inserito nel DOM altrimenti non avremmo accesso agli attributi "data-*" che di conseguenza sarebbero "undefined". 
    */
    connectedCallback() {
        appendContentTo(this);
    }
}

function appendContentTo(self) {

    const prodData = JSON.parse(self.dataset.product);
    const shadow = self.shadowRoot;
    const product = shadow.querySelector(".product");

    if (product.innerHTML !== "") {
        return;
    }

    var topLayer;

    if (!prodData.outOfStock) {
        topLayer = `<a class="center-align" href="${ '/Dettagli-Prodotto/' + prodData.id }"></a>`;
    } else {
        topLayer = `<span class="center-align out-of-stock">Esaurito</span>`;
    }

    const imgCont = `
        <div class="img-cont">
            <div class="img-canvas" style="background-image: url('/assets/images/${prodData.img}')"></div>
        </div>
    `;

    const prodDescription = `
        <div class="description">
            <p class="product-name">${prodData.name}</p>
            <p class="price">
                <span class="${prodData.discount ? 'line-through' : ''}">${prodData.price} €</span>
            </p>
        </div>
    `;

    const layer = `<div class="layer"></div>`;

    product.innerHTML += topLayer;
    product.innerHTML += imgCont;
    product.innerHTML += prodDescription;
    
    if (prodData.discount && !prodData.outOfStock) {
        const price = product.querySelector(".price");
        price.innerHTML += `<span class="discounted-price">${(prodData.price - ((prodData.price * prodData.discount) / 100))} €</span>`;

        const discount = `<div class="discount"><span class="discounted-rice">-${prodData.discount}%</span></div>`;

        product.innerHTML += discount;
    }

    product.innerHTML += layer;
}

// Define the new element
customElements.define('my-product', Product);