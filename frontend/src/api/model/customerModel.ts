export interface ListResult {
  code: number;
  data: {
    items: Array<CustomerModel>,
    currentPage: number,
    lastPage: number,
    pageSize: number,
    total: number,
    totalPage: number,
  };
  message: string;
}

export interface searchForm {
  name?: string;
  platform?: string;
}

export interface sortType {
  sortBy: string,
  descending: boolean,
}
export interface searchParameter {
  name?: string;
  email?: string;
  telephone?: string;
  platform?: string;
  page: number;
  pageSize: number;
  sortBy: string;
  descending: boolean;
}

export interface SearchRelatedParameter {
  customerId: number;
  page?: number;
  pageSize?: number;
}

export interface CustomerContact {
  name: string;
  email?: string;
  telephone?: string;
  qq?: string;
  wechat?: string;
  whatsapp?: string;
  note?: string;
}

export interface CustomerAddress {
  country: string;
  province?: string;
  city?: string;
  address?: string;
  zipcode?: string;
  other?: object;
}

export interface CustomerShippingAddress {
  contactName?: string,
  contactTelephone?: string,
  country: string;
  province?: string;
  city?: string;
  address?: string;
  zipcode?: string;
  other?: string;
}

export interface CustomerModel {
  name?: string;
  email?: string;
  telephone?: string;
  from?: string;
  type?: string;
  createBy?: string;
  createdAt?: string;
  updatedAt?: string;
  customerContact?: Array<CustomerContact>;
  customerAddress?: Array<CustomerAddress>;
  customerShippingAddress?: Array<CustomerShippingAddress>;
}
