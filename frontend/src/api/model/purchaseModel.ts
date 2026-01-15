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
  purchaseStatus?: number;
  purchaseStartedAt?: string;
  purchaseEndedAt?: string;
  page: number;
  pageSize: number;
  sortBy: string;
  descending: boolean;
}




