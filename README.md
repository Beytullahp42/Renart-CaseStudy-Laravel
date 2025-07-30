> **Note:** The hosted Back End API is available until **December 11, 2025**. After that date, the endpoint may be taken down or replaced.

# Ring Product List Backend (Laravel)

This is the backend application for a full-stack web case study built as part of my internship application for [Renart](https://www.renartglobal.com/). It reads product data from a local JSON file, fetches real-time gold gram prices via [MetalPriceAPI](https://metalpriceapi.com/), and exposes endpoints for listing and filtering products.


---

## ✨ Features

- **Fetch Products** — calculates each product’s dynamic price based on its weight, popularity score, and current gold price
- **Filter Products** — accepts min/max price and popularity (0–5 star scale) and returns matching items
- **Gold Price Endpoint** — returns the current USD gold gram price
- **In-memory Caching** — caches the gold price for 1 hour to minimize external API calls

---

## 🛠️ Technologies Used

- **Laravel 12**
- **MetalPriceAPI** for live gold prices

---

## 🔌 API Endpoints

| Method | URI               | Description                                                                             |
|--------|-------------------|-----------------------------------------------------------------------------------------|
| GET    | `/api/products`   | Fetch all products with calculated prices                                               |
| POST   | `/api/products`   | Filter products by `minPrice`, `maxPrice`, `minPopularity`, `maxPopularity` (stars 0–5) |
| GET    | `/api/gold-price` | Retrieve current gold gram price (USD)                                                  |

---

## 🔗 Links

- 📦 **Front End Repository:** https://github.com/Beytullahp42/Renart-CaseStudy-React
- 🌐 **Live Back End API:** https://renart-case-study-beytp.onrender.com/
- 🌐 **Live Front End:** https://renart-case-study-react.vercel.app/
