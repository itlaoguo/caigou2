export interface OrderProductModel {
  id?: number;
  orderId?: number;
  name?: string;
  image?: string;
  sku?: string;
  price?: string;
  currency?: string;
  quantity?: string;
  unit?: string;
  note?: string;
}

export interface ShippingAddressModel {
  contactName?: string;
  contactTelephone?: string;
  country?: string;
  countryCode?: string;
  province?: string;
  city?: string;
  address?: string;
  otherAddress: string;
  zipcode?: string;
  note?: string;
}



export interface OrderModel {
  number?: string;
  customerId?: string;
  customerName?: string;
  companyId?: number;
  companyName?: string;
  platformCode?: string;
  platform?: string;
  subject?: string;
  total?: string;
  payStatus?: string;
  note?: string;
  createdBy?: string;
  createAt?: string;
  updatedAt?: string;
  shippingAddress: ShippingAddressModel;
  productList?: Array<OrderProductModel>;
}



export interface ListResult {
  code: number;
  data: {
    items: Array<OrderModel>,
    currentPage: number,
    lastPage: number,
    pageSize: number,
    total: number,
    totalPage: number,
  };
  message: string;
}

export interface searchForm {
  customerName?: string;
  platform?: string;
}

export interface sortType {
  sortBy: string,
  descending: boolean,
}

export interface searchParameter {
  customerId?: number;
  name?: string;
  platform?: string;
  payStatus?: string,
  page?: number;
  pageSize?: number;
  sortBy?: string;
  descending?: boolean;
}


