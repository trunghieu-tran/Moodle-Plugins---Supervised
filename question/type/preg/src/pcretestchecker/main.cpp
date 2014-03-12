#include <QCoreApplication>
#include <QTextStream>
#include "pcre.h"

#define max(a, b) (a > b ? a : b)

QTextStream qin(stdin);
QTextStream qout(stdout);

void reset_ovector(int ovector[]) {
    for (int j = 0; j < 1024; j++) {
        ovector[j] = -1;
    }
}

bool compare_match(QString methodname, QString regex, int options, QString string, const int oexpected[], const int oobtained[], int count_expected, int count_obtained, bool partial)
{
    if (partial) {
        count_expected = 1;
        count_obtained = 1;
    }

    bool passed = (count_expected == count_obtained);
    for (int i = 0; passed && i < 2 * count_expected; i++) {
        passed &= (oexpected[i] == oobtained[i]);
    }

    if (passed) {
        return true;
    }

    qout << "==================================================\n";
    if (partial) {
        qout << "failed partial match comparison\n";
    } else {
        qout << "failed full match comparison\n";
    }

    qout << "method name: " << methodname << "\n";
    qout << "regex: " << regex << "\n";
    qout << "options: " << options << "\n";
    qout << "string: " << string << "\n";
    qout << "expected " << count_expected << " subexpression(s):\n";
    for (int i = 0; i < 2 * count_expected; i += 2) {
        if (oexpected[i] != -1) {
            qout << i / 2 << ": (" << oexpected[i] << "," << oexpected[i + 1] - oexpected[i] << ") ";
        }
    }
    qout << "\n";
    qout << "obtained " << count_obtained << " subexpression(s):\n";
    for (int i = 0; i < 2 * count_obtained; i += 2) {
        if (oobtained[i] != -1) {
            qout << i / 2 << ": (" << oobtained[i] << "," << oobtained[i + 1] - oobtained[i] << ") ";
        }
    }
    qout << "\n";
    return false;
}

int main(int argc, char *argv[])
{
    qin.setCodec("UTF-8");
    qin.autoDetectUnicode();

    qout.setCodec("UTF-8");
    qout.autoDetectUnicode();

    int failed = 0;
    int passed = 0;

    // Test method name length and method name itself
    int methodname_length;

    qin >> methodname_length;
    qin.read(1);

    QString methodname = qin.read(methodname_length);
    qin.read(1);

    //qout << "methodname: " << methodname << "\n";

    // Regex length and regex itself
    int regex_length;

    qin >> regex_length;
    qin.read(1);

    //qout << "regex_length: " << regex_length << "\n";

    QString regex = qin.read(regex_length);
    qin.read(1);

    //qout << "regex: " << regex << "\n";

    // Regex options
    int regex_options;

    qin >> regex_options;
    qin.read(1);
    regex_options = regex_options | PCRE_UTF8;

    // Compile pattern
    const char * error;
    int erroffset;
    pcre * re = pcre_compile(regex.toStdString().data(), regex_options, &error, &erroffset, NULL);
    if (!re) {
        qout << "compilation failed: " << error << "\n";
        qout << "method name: " << methodname << "\n";
        qout << "regex: " << regex << "\n";
        qout << "options: " << regex_options << "\n";
        return 1;
    }

    // Number of tests and tests themselves
    int tests_count;

    qin >> tests_count;
    qin.read(1);

    for (int i = 0; i < tests_count; i++) {
        // String length and string itself
        int string_length;

        qin >> string_length;
        qin.read(1);

        QString string = qin.read(string_length);
        qin.read(1);
        QByteArray stringBytes = string.toUtf8();

        //qout << "string: " << string << "\n";

        // Number of subexpressions and offsets
        int subexprs_count;
        int subexpr_max = 0;
        int subexprs[1024];
        reset_ovector(subexprs);

        qin >> subexprs_count;
        qin.read(1);

        for (int j = 0; j < subexprs_count; j++) {
            int _subexpr, _index, _length;
            qin >> _subexpr;
            qin.read(1);
            qin >> _index;
            qin.read(1);
            qin >> _length;
            qin.read(1);

            // Convert utf8 offsets to plain char* offsets
            if (string.size() != stringBytes.size()) {
                int _newIndex;
                int _newLength;
                if (_index > 0) {
                    QString tmp = string.left(_index);
                    _newIndex = tmp.toUtf8().size();
                }
                if (_index + _length > 0) {
                    QString tmp = string.mid(_index, _length);
                    _newLength = tmp.toUtf8().size();
                }
                _index = _newIndex;
                _length = _newLength;
            }

            subexprs[2 * _subexpr] = _index;
            subexprs[2 * _subexpr + 1] = _index + _length;
            subexpr_max = max(subexpr_max, _subexpr);
        }

        // Process the test.
        int count = 0;
        int ovector[1024];
        reset_ovector(ovector);
        int match_options = PCRE_PARTIAL;
        int longest_count = 0;
        int leftborder = string_length == 0 ? 0 : 1;

        // Here be a terrible code beacuse PCRE can't find partial matches if the string contains some unmatched suffix
        for (int len = string_length; len >= leftborder; len--) {
            int cur_ovector[1024];
            QString choppedString = string.left(len);
            int bytesCount = choppedString.toUtf8().size();
            count = pcre_exec(re, NULL, choppedString.toStdString().data(), bytesCount, 0, match_options, cur_ovector, 1024);

            if (count != PCRE_ERROR_PARTIAL && count <= 0) {
                // Match not found
                continue;
            }

            if (ovector[0] == -1 || cur_ovector[1] - cur_ovector[0] >= ovector[1] - ovector[0]) {  // leftmost longest match found
                std::copy(cur_ovector, cur_ovector + 1024, ovector);
                longest_count = count;
            }

            if (len == string_length && count > 0) {
                // A full match found in the whole string, break the loop
                break;
            }
        }

        bool current_passed;

        if (longest_count == PCRE_ERROR_PARTIAL) {
            // Partial match
            current_passed = compare_match(methodname, regex, regex_options, string, subexprs, ovector, subexpr_max + 1, longest_count, true);
        } else if (longest_count > 0) {
            // Full match
            current_passed = compare_match(methodname, regex, regex_options, string, subexprs, ovector, subexpr_max + 1, longest_count, false);
        } else {
            // No match
            current_passed = compare_match(methodname, regex, regex_options, string, subexprs, ovector, subexpr_max + 1, 1, false);
        }
        if (current_passed) {
            passed++;
        } else {
            if (string.size() != stringBytes.size()) {
                qout << "(utf8 offsets were converted to plain char* offsets\n";
            }
            failed++;
        }
    }

    //qout << "==================================================\n";
    //qout << "passed: " << passed<< "\n";
    //qout << "failed: " << failed << "\n";

    return 0;
}
