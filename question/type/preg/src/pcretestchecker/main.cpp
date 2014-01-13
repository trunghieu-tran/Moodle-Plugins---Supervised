#include <stdio.h>
#include <string.h>
#include "pcre.h"
#include <iostream>

#define max(a, b) (a > b ? a : b)

void reset_ovector(int ovector[]) {
    for (int j = 0; j < 1024; j++) {
        ovector[j] = -1;
    }
}

void compare_match(const char * methodname, const char * regex, int options, const char * string, const int oexpected[], const int oobtained[], int count_expected, int count_obtained, bool partial)
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
        return;
    }

    std::cout << "==================================================\n";
    if (partial) {
        std::cout << "failed partial match comparison\n";
    } else {
        std::cout << "failed full match comparison\n";
    }

    std::cout << "method name: " << methodname << "\n";
    std::cout << "regex: " << regex << "\n";
    std::cout << "options: " << options << "\n";
    std::cout << "string: " << string << "\n";
    std::cout << "expected " << count_expected << " subexpression(s):\n";
        for (int i = 0; i < 2 * count_expected; i += 2) {
            if (oexpected[i] != -1) {
                std::cout << i / 2 << ": (" << oexpected[i] << "," << oexpected[i + 1] - oexpected[i] << ") ";
            }
        }
        std::cout << "\n";
        std::cout << "obtained " << count_obtained << " subexpression(s):\n";
        for (int i = 0; i < 2 * count_obtained; i += 2) {
            if (oobtained[i] != -1) {
                std::cout << i / 2 << ": (" << oobtained[i] << "," << oobtained[i + 1] - oobtained[i] << ") ";
            }
        }
        std::cout << "\n";
}

int main (int argc, char *argv[])
{
    char tmp;

    // Test method name length and method name itself
    int methodname_length;
    char methodname[1024 * 1024];

    std::cin >> methodname_length;
    std::cin.get(tmp);

    std::cin.get(methodname, methodname_length + 1, EOF);
    std::cin.get(tmp);

    // Regex length and regex itself
    int regex_length;
    char regex[1024 * 1024];

    std::cin >> regex_length;
    std::cin.get(tmp);

    std::cin.get(regex, regex_length + 1, EOF);
    std::cin.get(tmp);

    // Regex options
    int regex_options;

    std::cin >> regex_options;
    std::cin.get(tmp);

    // Compile pattern
    const char * error;
    int erroffset;
    pcre * re = pcre_compile(regex, regex_options, &error, &erroffset, NULL);
    if (!re) {
        std::cout << "compilation failed: " << error << "\n";
        std::cout << "regex: " << regex << "\n";
        std::cout << "options: " << regex_options << "\n";
        return 1;
    }

    // Number of tests and tests themselves
    int tests_count;

    std::cin >> tests_count;
    std::cin.get(tmp);

    for (int i = 0; i < tests_count; i++) {
        // String length and string itself
        int string_length;
        char string[1024 * 1024];

        std::cin >> string_length;
        std::cin.get(tmp);

        if (string_length > 0) {
            std::cin.get(string, string_length + 1, EOF);
        }
        std::cin.get(tmp);

        // Number of subexpressions and offsets
        int subexprs_count;
        int subexpr_max = 0;
        int subexprs[1024];
        reset_ovector(subexprs);

        std::cin >> subexprs_count;
        std::cin.get(tmp);

        for (int j = 0; j < subexprs_count; j++) {
            int _subexpr, _index, _length;
            std::cin >> _subexpr;
            std::cin.get(tmp);
            std::cin >> _index;
            std::cin.get(tmp);
            std::cin >> _length;
            std::cin.get(tmp);
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
        int leftborder = string_length == 0
                       ? 0
                       : 1;

        // Here be a terrible code beacuse PCRE can't find partial matches if the string contains some unmatched suffix
        for (int len = string_length; len >= leftborder; len--) {
            int cur_ovector[1024];
            count = pcre_exec(re, NULL, string, len, 0, match_options, cur_ovector, 1024);

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

        if (longest_count == PCRE_ERROR_PARTIAL) {
            // Partial match
            compare_match(methodname, regex, regex_options, string, subexprs, ovector, subexpr_max + 1, longest_count, true);
        } else if (longest_count > 0) {
            // Full match
            compare_match(methodname, regex, regex_options, string, subexprs, ovector, subexpr_max + 1, longest_count, false);
        } else {
            // No match
            compare_match(methodname, regex, regex_options, string, subexprs, ovector, subexpr_max + 1, 1, false);
        }

    }

    return 0;
}
