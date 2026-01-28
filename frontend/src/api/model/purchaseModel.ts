export interface ListResult {
  code: number;
  data: {
    items: Array<PurchaseOrderModel>,
    currentPage: number,
    lastPage: number,
    pageSize: number,
    total: number,
    totalPage: number,
  };
  message: string;
}

export interface PurchaseOrderModel {
  id?: number;
  number?: string;
  name?: string;
  status?: number;
  created_at?: string;
  updated_at?: string;
}

export type SearchParameter = {
  purchaseNumber?: string;
  purchaseName?: string;
  purchaseStatus?: string;
  purchaseStartedAt?: string;
  purchaseEndedAt?: string;
  page: number;
  pageSize: number;
  sortBy: string;
  descending: boolean;
}

export interface BaseInfo {
  enter_way: string
  name: string
  description: string
}

export interface UploadInfo {
  name?: string;
  path?: string;
  url?: string;
  size?: number;
  uploadTime?: string;
}

export interface Product {
  product_id: string
  product_title: string
  sku_id: string
  sku_title: string
  price: number
  product_pic_url: string
  purchaser_id: string
  quantity: number
  can_sell: number
  address_detail: string
  receiver: string
  receiver_phone: string
}

export interface CreatePurchaseOrder {
  baseInfo: BaseInfo
  uploadInfo: UploadInfo
  productList: Product[]
  is_finished: boolean
}




