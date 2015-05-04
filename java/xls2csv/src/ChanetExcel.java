import java.io.File;
import java.io.FileInputStream;
import java.io.InputStream;

import org.apache.poi.ss.usermodel.Cell;
import org.apache.poi.ss.usermodel.DateUtil;
import org.apache.poi.ss.usermodel.Row;
import org.apache.poi.ss.usermodel.Sheet;
import org.apache.poi.ss.usermodel.Workbook;
import org.apache.poi.ss.usermodel.WorkbookFactory;

public class ChanetExcel {

	public static void main(String[] args) throws Exception {

		if (args.length != 1) {
			System.out.println(" file name required.");
			return;
		}

		File f = new File(args[0]);

		if (true != f.exists()) {
			System.out.println(args[0] + " not found");
			return;
		}

		InputStream is = new FileInputStream(f);

		Workbook wb = WorkbookFactory.create(is);
		// # WorkbookFactory
		// //get到Sheet对象
		Sheet sheet = wb.getSheetAt(0);
		// //这个必须用接口
		for (Row row : sheet) {
			int rn = row.getLastCellNum();
			for (Cell cell : row) {
				// //cell.getCellType是获得cell里面保存的值的type
				// //如Cell.CELL_TYPE_STRING
				// //cell.getCellType是获得cell里面保存的值的type
				// //如Cell.CELL_TYPE_STRING
				switch (cell.getCellType()) {
				// case Cell.CELL_TYPE_BOOLEAN:
				// //得到Boolean对象的方法
				// System.out.print(cell.getBooleanCellValue()+" ");
				// break;
				case Cell.CELL_TYPE_NUMERIC:
					// //先看是否是日期格式
					if (DateUtil.isCellDateFormatted(cell)) {
						// 读取日期格式
						System.out.print("\"" + cell.getDateCellValue() + "\"");
					} else {
						// 读取数字
						System.out.print((int) cell.getNumericCellValue());
					}
					break;
				case Cell.CELL_TYPE_FORMULA:
					// 读取公式
					System.out.print(cell.getCellFormula());
					break;
				case Cell.CELL_TYPE_STRING:
					// 读取String
					String s = cell.getRichStringCellValue().toString();
					if (s.length() > 0) {
						System.out.print("\"" + s + "\"");
					} else {
						System.out.print("\"\"");
					}
					break;
				default:
					System.out.print("\"NUL\"");
				}
				rn = rn - 1;
				if (rn > 0) {
					System.out.print(",");
				}
			}
			System.out.println("");

		}

	}
}
