class Budget {
  int[][] values;
  String year;
  int total;
  int deficit;
  int incomes;
  Budget(String s, int[][] v, int t, int d) {
    year = s;
    values = v;
    total = t;
    deficit = d;
    incomes = t-d;
  }
}
